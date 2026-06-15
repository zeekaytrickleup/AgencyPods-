<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\InteractsWithPods;
use App\Models\Client;
use App\Models\WeeklyTask;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class WeeklyController extends Controller
{
    use InteractsWithPods;

    public function index(Request $request)
    {
        $podIds = $this->visiblePods()->pluck('id');

        $clients = Client::whereIn('pod_id', $podIds)->orderBy('name')->get();

        // Which week are we viewing? Query param wins; otherwise the most
        // recent week that has tasks; otherwise the current week.
        if ($request->filled('week')) {
            $weekStart = Carbon::parse($request->week)->startOfWeek();
        } else {
            $latest = WeeklyTask::whereHas('client', fn ($q) => $q->whereIn('pod_id', $podIds))
                ->max('week_start');
            $weekStart = $latest ? Carbon::parse($latest)->startOfWeek() : Carbon::now()->startOfWeek();
        }

        $weekTasks = WeeklyTask::with('client')
            ->whereHas('client', fn ($q) => $q->whereIn('pod_id', $podIds))
            ->whereDate('week_start', $weekStart->toDateString())
            ->orderBy('id')
            ->get();

        return view('pods.weekly', [
            'weekTasks' => $weekTasks,
            'clients' => $clients,
            'weekStart' => $weekStart,
            'prevWeek' => $weekStart->copy()->subWeek()->toDateString(),
            'nextWeek' => $weekStart->copy()->addWeek()->toDateString(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
            'task' => ['required', 'string', 'max:255'],
            'week_start' => ['required', 'date'],
        ]);

        $client = Client::findOrFail($data['client_id']);
        $this->authorizeClient($client);

        $week = Carbon::parse($data['week_start'])->startOfWeek()->toDateString();

        $client->weeklyTasks()->create([
            'task' => $data['task'],
            'status' => 'pending',
            'week_start' => $week,
        ]);

        return redirect()->route('weekly.index', ['week' => $week])->with('status', 'Task added.');
    }

    public function update(Request $request, WeeklyTask $task)
    {
        $this->authorizeClient($task->client);

        $data = $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
            'task' => ['required', 'string', 'max:255'],
        ]);

        $client = Client::findOrFail($data['client_id']);
        $this->authorizeClient($client);

        $task->update([
            'client_id' => $client->id,
            'task' => $data['task'],
        ]);

        return redirect()->route('weekly.index', ['week' => $task->week_start->toDateString()])
            ->with('status', 'Task updated.');
    }

    public function toggle(WeeklyTask $task)
    {
        $this->authorizeClient($task->client);

        $task->update([
            'status' => $task->status === 'done' ? 'pending' : 'done',
        ]);

        return redirect()->route('weekly.index', ['week' => $task->week_start->toDateString()])
            ->with('status', 'Task updated.');
    }

    public function destroy(WeeklyTask $task)
    {
        $this->authorizeClient($task->client);

        $week = $task->week_start->toDateString();
        $task->delete();

        return redirect()->route('weekly.index', ['week' => $week])->with('status', 'Task deleted.');
    }
}
