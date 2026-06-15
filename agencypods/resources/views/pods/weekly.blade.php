@extends('layouts.pods')

@section('title', 'Weekly goals')

@php
    $done = $weekTasks->where('status', 'done')->count();
    $total = $weekTasks->count();
@endphp

@section('content')
<div class="section-header">
    <div style="display:flex;align-items:center;gap:10px">
        <a class="btn" href="{{ route('weekly.index', ['week' => $prevWeek]) }}" title="Previous week"><i class="ti ti-chevron-left" aria-hidden="true"></i></a>
        <span class="section-title" style="min-width:150px;text-align:center">Week of {{ $weekStart->format('j M Y') }}</span>
        <a class="btn" href="{{ route('weekly.index', ['week' => $nextWeek]) }}" title="Next week"><i class="ti ti-chevron-right" aria-hidden="true"></i></a>
    </div>
    @if($clients->isNotEmpty())
        <button class="btn btn-primary" onclick="document.getElementById('addTaskDlg').showModal()">
            <i class="ti ti-plus" aria-hidden="true"></i> Add task
        </button>
    @endif
</div>

<div style="display:flex;gap:8px;margin-bottom:16px">
    <div class="metric-card" style="flex:1"><div class="metric-label">Total</div><div class="metric-value">{{ $total }}</div></div>
    <div class="metric-card" style="flex:1"><div class="metric-label">Done</div><div class="metric-value" style="color:#3B6D11">{{ $done }}</div></div>
    <div class="metric-card" style="flex:1"><div class="metric-label">Pending</div><div class="metric-value" style="color:#854F0B">{{ $total - $done }}</div></div>
</div>

@if($clients->isEmpty())
    <div class="empty-state">No clients in your pods yet — add a client before creating weekly tasks.</div>
@elseif($weekTasks->isEmpty())
    <div class="empty-state">No tasks for this week. Use “Add task” to create one.</div>
@else
    <table class="weekly-table">
        <thead><tr><th>Client</th><th>Task</th><th>Status</th><th style="text-align:right">Actions</th></tr></thead>
        <tbody>
            @foreach($weekTasks as $task)
                <tr>
                    <td style="font-weight:500">{{ $task->client->name }}</td>
                    <td>{{ $task->task }}</td>
                    <td><span class="status-pill {{ $task->status === 'done' ? 'status-done' : 'status-pending' }}">{{ $task->status === 'done' ? 'Done' : 'Pending' }}</span></td>
                    <td>
                        <div style="display:flex;gap:4px;justify-content:flex-end">
                            <form method="POST" action="{{ route('weekly.toggle', $task) }}">
                                @csrf @method('PATCH')
                                <button class="icon-btn" type="submit" title="{{ $task->status === 'done' ? 'Mark pending' : 'Mark done' }}">
                                    <i class="ti ti-{{ $task->status === 'done' ? 'arrow-back-up' : 'check' }}" aria-hidden="true"></i>
                                </button>
                            </form>
                            <button class="icon-btn" type="button" title="Edit"
                                onclick="editTask({{ $task->id }}, {{ $task->client_id }}, @js($task->task))"><i class="ti ti-pencil" aria-hidden="true"></i></button>
                            <form method="POST" action="{{ route('weekly.destroy', $task) }}" onsubmit="return confirm('Delete this task?')">
                                @csrf @method('DELETE')
                                <button class="icon-btn" type="submit" title="Delete"><i class="ti ti-trash" aria-hidden="true"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif

@if($clients->isNotEmpty())
    {{-- Add task dialog --}}
    <dialog id="addTaskDlg">
        <form method="POST" action="{{ route('weekly.store') }}" class="dlg-body">
            @csrf
            <input type="hidden" name="week_start" value="{{ $weekStart->toDateString() }}">
            <div class="dlg-title">Add task — week of {{ $weekStart->format('j M Y') }}</div>
            <div class="field">
                <label>Client</label>
                <select name="client_id" required>
                    @foreach($clients as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field"><label>Task</label><input name="task" required autofocus placeholder="e.g. Publish summer landing page"></div>
            <div class="dlg-actions">
                <button class="btn" type="button" onclick="this.closest('dialog').close()">Cancel</button>
                <button class="btn btn-primary" type="submit">Add task</button>
            </div>
        </form>
    </dialog>

    {{-- Edit task dialog --}}
    <dialog id="editTaskDlg">
        <form method="POST" id="editTaskForm" class="dlg-body">
            @csrf @method('PUT')
            <div class="dlg-title">Edit task</div>
            <div class="field">
                <label>Client</label>
                <select name="client_id" id="editTaskClient" required>
                    @foreach($clients as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field"><label>Task</label><input name="task" id="editTaskName" required></div>
            <div class="dlg-actions">
                <button class="btn" type="button" onclick="this.closest('dialog').close()">Cancel</button>
                <button class="btn btn-primary" type="submit">Save</button>
            </div>
        </form>
    </dialog>
@endif
@endsection

@push('scripts')
<script>
function editTask(id, clientId, task) {
    var f = document.getElementById('editTaskForm');
    f.action = '{{ url('weekly-tasks') }}/' + id;
    document.getElementById('editTaskClient').value = clientId;
    document.getElementById('editTaskName').value = task;
    document.getElementById('editTaskDlg').showModal();
}
</script>
@endpush
