<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\InteractsWithPods;
use App\Models\Client;
use App\Models\Pod;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    use InteractsWithPods;

    public function store(Request $request)
    {
        $data = $request->validate([
            'pod_id' => ['required', 'exists:pods,id'],
            'name' => ['required', 'string', 'max:255'],
            'industry' => ['nullable', 'string', 'max:255'],
        ]);

        $pod = Pod::findOrFail($data['pod_id']);
        $this->authorizePod($pod);

        $client = $pod->clients()->create([
            'name' => $data['name'],
            'industry' => $data['industry'] ?? null,
        ]);

        return redirect()->route('dashboard', ['pod' => $pod->id, 'client' => $client->id])
            ->with('status', 'Client added.');
    }

    public function update(Request $request, Client $client)
    {
        $this->authorizeClient($client);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'industry' => ['nullable', 'string', 'max:255'],
        ]);

        $client->update($data);

        return redirect()->route('dashboard', ['pod' => $client->pod_id, 'client' => $client->id])
            ->with('status', 'Client updated.');
    }

    public function destroy(Client $client)
    {
        $this->authorizeClient($client);

        $podId = $client->pod_id;
        $client->delete();

        return redirect()->route('dashboard', ['pod' => $podId])
            ->with('status', 'Client deleted.');
    }
}
