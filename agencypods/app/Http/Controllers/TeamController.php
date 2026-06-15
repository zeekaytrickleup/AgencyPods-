<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class TeamController extends Controller
{
    // Routes gated by `role:pod_manager`.
    public function index(Request $request)
    {
        return view('pods.team.index', [
            'team' => $request->user()->teamMembers()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', Password::min(8)],
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'team_member',
            'manager_id' => $request->user()->id,
        ]);

        return redirect()->route('team.index')->with('status', 'Team member added.');
    }

    public function destroy(Request $request, User $user)
    {
        // A manager may only remove their own team members.
        abort_unless($user->role === 'team_member' && $user->manager_id === $request->user()->id, 403);

        $user->delete();

        return redirect()->route('team.index')->with('status', 'Team member removed.');
    }
}
