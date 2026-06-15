@extends('layouts.pods')

@section('title', 'Reports')

@php $p = $period; @endphp

@section('content')
{{-- ===== Filter bar ===== --}}
<div style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:18px">
    <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
        <div style="display:flex;gap:4px;background:var(--card);border:1px solid var(--border);border-radius:var(--r-sm);padding:3px">
            <a class="btn {{ $p['type'] === 'week' ? 'btn-primary' : '' }}" style="border:none;{{ $p['type'] === 'week' ? '' : 'background:transparent;box-shadow:none' }}"
               href="{{ route('reports.index', ['period' => 'week', 'date' => $p['refDate']]) }}">Weekly</a>
            <a class="btn {{ $p['type'] === 'month' ? 'btn-primary' : '' }}" style="border:none;{{ $p['type'] === 'month' ? '' : 'background:transparent;box-shadow:none' }}"
               href="{{ route('reports.index', ['period' => 'month', 'date' => $p['refDate']]) }}">Monthly</a>
        </div>
        <div style="display:flex;align-items:center;gap:8px">
            <a class="btn" href="{{ route('reports.index', ['period' => $p['type'], 'date' => $p['prevDate']]) }}" title="Previous"><i class="ti ti-chevron-left" aria-hidden="true"></i></a>
            <span class="section-title" style="min-width:150px;text-align:center">{{ $p['label'] }}</span>
            <a class="btn" href="{{ route('reports.index', ['period' => $p['type'], 'date' => $p['nextDate']]) }}" title="Next"><i class="ti ti-chevron-right" aria-hidden="true"></i></a>
        </div>
    </div>
    <a class="btn btn-primary" href="{{ route('reports.pdf', ['period' => $p['type'], 'date' => $p['refDate']]) }}">
        <i class="ti ti-download" aria-hidden="true"></i> Download {{ ucfirst($p['type']) }}ly PDF
    </a>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:12px;margin-bottom:18px">
    <div class="metric-card"><div class="metric-label">Total pods</div><div class="metric-value">{{ $totalPods }}</div></div>
    <div class="metric-card"><div class="metric-label">Total clients</div><div class="metric-value">{{ $totalClients }}</div></div>
    <div class="metric-card"><div class="metric-label">Goals created</div><div class="metric-value">{{ $goalsCreated }}</div></div>
    <div class="metric-card"><div class="metric-label">Tasks done</div><div class="metric-value">{{ $weeklyDone }}<span style="font-size:15px;color:var(--t3)">/{{ $weeklyTotal }}</span></div></div>
</div>

<div class="section-header"><span class="section-title">Per pod — {{ $p['label'] }}</span></div>

<table class="weekly-table">
    <thead><tr><th>Pod</th><th>Manager</th><th>Clients</th><th>Goals</th><th>Tasks done</th><th style="text-align:right">Export</th></tr></thead>
    <tbody>
        @foreach($podStats as $row)
            <tr>
                <td style="font-weight:600"><span style="display:inline-block;width:9px;height:9px;border-radius:50%;background:{{ $row['pod']->color }};margin-right:7px"></span>{{ $row['pod']->name }}</td>
                <td style="color:var(--t2)">{{ optional($row['pod']->manager)->name ?? 'Unassigned' }}</td>
                <td>{{ $row['clients'] }}</td>
                <td>{{ $row['goals'] }}</td>
                <td>
                    @if($row['tasksTotal'])
                        <span class="status-pill {{ $row['tasksDone'] === $row['tasksTotal'] ? 'status-done' : 'status-pending' }}">{{ $row['tasksDone'] }}/{{ $row['tasksTotal'] }}</span>
                    @else
                        <span style="color:var(--t3)">—</span>
                    @endif
                </td>
                <td>
                    <div style="display:flex;justify-content:flex-end">
                        <a class="btn" style="padding:5px 10px;font-size:11px" href="{{ route('reports.pod.pdf', ['pod' => $row['pod']->id, 'period' => $p['type'], 'date' => $p['refDate']]) }}"><i class="ti ti-download" style="font-size:12px" aria-hidden="true"></i> PDF</a>
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
