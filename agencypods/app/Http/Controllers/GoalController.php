<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\InteractsWithPods;
use App\Models\Client;
use App\Models\Goal;
use Illuminate\Http\Request;

class GoalController extends Controller
{
    use InteractsWithPods;

    public function store(Request $request)
    {
        $data = $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
            'title' => ['required', 'string', 'max:255'],
        ]);

        $client = Client::findOrFail($data['client_id']);
        $this->authorizeClient($client);

        $goal = $client->goals()->create(['title' => $data['title']]);

        // Every goal owns the four sections up front.
        foreach (Goal::SECTION_TYPES as $type) {
            $goal->sections()->create(['type' => $type, 'content' => null]);
        }

        return redirect()->route('dashboard', [
            'pod' => $client->pod_id,
            'client' => $client->id,
            'open' => $goal->id,
        ])->with('status', 'Goal added.');
    }

    public function update(Request $request, Goal $goal)
    {
        $this->authorizeGoal($goal);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);

        $goal->update($data);

        return redirect()->route('dashboard', [
            'pod' => $goal->client->pod_id,
            'client' => $goal->client_id,
            'open' => $goal->id,
        ])->with('status', 'Goal updated.');
    }

    public function destroy(Goal $goal)
    {
        $this->authorizeGoal($goal);

        $client = $goal->client;
        $goal->delete();

        return redirect()->route('dashboard', [
            'pod' => $client->pod_id,
            'client' => $client->id,
        ])->with('status', 'Goal deleted.');
    }
}
