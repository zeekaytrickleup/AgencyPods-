<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\InteractsWithPods;
use App\Models\GoalSection;
use Illuminate\Http\Request;

class GoalSectionController extends Controller
{
    use InteractsWithPods;

    public function update(Request $request, GoalSection $section)
    {
        $this->authorizeSection($section);

        $data = $request->validate([
            'content' => ['nullable', 'string'],
        ]);

        $section->update(['content' => $data['content']]);

        $goal = $section->goal;

        return redirect()->route('dashboard', [
            'pod' => $goal->client->pod_id,
            'client' => $goal->client_id,
            'open' => $goal->id,
            'tab' => $section->type,
        ])->with('status', 'Saved.');
    }
}
