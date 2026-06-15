<?php

namespace App\Http\Controllers;

use App\Models\Pod;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PodController extends Controller
{
    // Routes gated by `role:super_admin`.
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'manager_id' => ['nullable', 'exists:users,id'],
        ]);

        Pod::create([
            'name' => $data['name'],
            'color' => $data['color'],
            'manager_id' => $data['manager_id'] ?: null,
        ]);

        return redirect()->route('users.index')->with('status', 'Pod created.');
    }

    public function destroy(Pod $pod)
    {
        $pod->delete();

        return redirect()->route('users.index')->with('status', 'Pod deleted.');
    }
}
