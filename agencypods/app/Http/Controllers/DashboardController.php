<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\InteractsWithPods;
use App\Models\Pod;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use InteractsWithPods;

    public function index(Request $request)
    {
        $pods = $this->visiblePods();

        // Resolve the current pod (query param if visible, else the first).
        $currentPod = $request->filled('pod')
            ? $pods->firstWhere('id', (int) $request->pod)
            : null;
        $currentPod ??= $pods->first();

        $clients = collect();
        $currentClient = null;

        if ($currentPod) {
            $currentPod->load([
                'clients' => fn ($q) => $q->orderBy('id'),
                'clients.goals' => fn ($q) => $q->orderBy('id'),
                'clients.goals.sections.attachments',
            ]);

            $clients = $currentPod->clients;

            $currentClient = $request->filled('client')
                ? $clients->firstWhere('id', (int) $request->client)
                : null;
            $currentClient ??= $clients->first();
        }

        return view('pods.dashboard', [
            'currentPod' => $currentPod,
            'clients' => $clients,
            'currentClient' => $currentClient,
            'openGoalId' => (int) $request->query('open', 0),
            'openTab' => $request->query('tab', 'goal'),
        ]);
    }
}
