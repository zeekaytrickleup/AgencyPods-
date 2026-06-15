@extends('pods.pdf.layout')

@section('doc-title'){{ ucfirst($period['type']) }}ly Report @endsection
@section('doc-sub'){{ $period['label'] }}@endsection

@section('styles')
  .metrics { width:100%; border-collapse:collapse; margin: 4px 0 18px; }
  .metrics td { width:25%; border:1px solid #ECECEF; padding:12px; text-align:center; }
  .metric-label { color:#8a8a90; font-size:9.5px; text-transform:uppercase; letter-spacing:.04em; }
  .metric-value { font-size:22px; font-weight:bold; padding-top:5px; color:#0a0a0f; }
  table.list { width:100%; border-collapse:collapse; margin-top:4px; }
  table.list th { text-align:left; font-size:9.5px; text-transform:uppercase; color:#8a8a90; border-bottom:1px solid #d9d9de; padding:8px; }
  table.list td { padding:9px 8px; border-bottom:1px solid #ECECEF; }
  table.list tr:nth-child(even) td { background:#fbfbfc; }
  .dot { display:inline-block; width:9px; height:9px; border-radius:50%; margin-right:6px; }
  .section-title { font-size:10.5px; text-transform:uppercase; color:#8a8a90; letter-spacing:.05em; margin:16px 0 6px; font-weight:bold; }
@endsection

@section('content')
  <table class="metrics">
    <tr>
      <td><div class="metric-label">Total pods</div><div class="metric-value">{{ $totalPods }}</div></td>
      <td><div class="metric-label">Total clients</div><div class="metric-value">{{ $totalClients }}</div></td>
      <td><div class="metric-label">Goals created</div><div class="metric-value">{{ $goalsCreated }}</div></td>
      <td><div class="metric-label">Tasks done</div><div class="metric-value">{{ $weeklyDone }}/{{ $weeklyTotal }}</div></td>
    </tr>
  </table>

  <div class="section-title">Per pod — {{ $period['label'] }}</div>
  <table class="list">
    <thead><tr><th>Pod</th><th>Manager</th><th>Clients</th><th>Goals</th><th>Tasks done</th></tr></thead>
    <tbody>
      @foreach($podStats as $row)
        <tr>
          <td><span class="dot" style="background:{{ $row['pod']->color }}"></span>{{ $row['pod']->name }}</td>
          <td>{{ optional($row['pod']->manager)->name ?? 'Unassigned' }}</td>
          <td>{{ $row['clients'] }}</td>
          <td>{{ $row['goals'] }}</td>
          <td>{{ $row['tasksTotal'] ? $row['tasksDone'].'/'.$row['tasksTotal'] : '—' }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
@endsection
