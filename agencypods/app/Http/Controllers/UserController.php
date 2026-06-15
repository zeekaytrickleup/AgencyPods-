<?php

namespace App\Http\Controllers;

use App\Models\Pod;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    // Routes gated by `role:super_admin`.
    public function index()
    {
        return view('pods.users.index', [
            'users' => User::with(['pods', 'manager'])->orderBy('role')->orderBy('name')->get(),
            'pods' => Pod::with('manager')->orderBy('id')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', Password::min(8)],
            'role' => ['required', Rule::in(['super_admin', 'pod_manager'])],
            'pods' => ['array'],
            'pods.*' => ['integer', 'exists:pods,id'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
        ]);

        $this->syncPods($user, $data['role'] === 'pod_manager' ? ($data['pods'] ?? []) : []);

        return redirect()->route('users.index')->with('status', 'User created.');
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', Password::min(8)],
            'role' => ['required', Rule::in(['super_admin', 'pod_manager'])],
            'pods' => ['array'],
            'pods.*' => ['integer', 'exists:pods,id'],
        ]);

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
        ]);

        if (! empty($data['password'])) {
            $user->update(['password' => Hash::make($data['password'])]);
        }

        $this->syncPods($user, $data['role'] === 'pod_manager' ? ($data['pods'] ?? []) : []);

        return redirect()->route('users.index')->with('status', 'User updated.');
    }

    public function destroy(Request $request, User $user)
    {
        abort_if($user->id === $request->user()->id, 403, 'You cannot delete your own account here.');

        $user->delete();

        return redirect()->route('users.index')->with('status', 'User deleted.');
    }

    /**
     * Make the given user the manager of exactly the selected pods,
     * releasing any pods they previously managed but no longer should.
     */
    private function syncPods(User $user, array $podIds): void
    {
        Pod::where('manager_id', $user->id)->whereNotIn('id', $podIds)->update(['manager_id' => null]);

        if (! empty($podIds)) {
            Pod::whereIn('id', $podIds)->update(['manager_id' => $user->id]);
        }
    }
}
