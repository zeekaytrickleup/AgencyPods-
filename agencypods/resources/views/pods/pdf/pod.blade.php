@extends('pods.pdf.layout')

@section('doc-title'){{ $pod->name }}@endsection
@section('doc-sub')Pod report &middot; {{ $period['label'] }}@endsection

@section('styles')
  .pod-head { font-size:15px; font-weight:bold; margin-bottom:2px; }
  .pod-head .dot { display:inline-block; width:11px; height:11px; border-radius:50%; margin-right:6px; }
  .muted { color:#8a8a90; font-size:10px; margin-bottom:6px; }
  .metrics { width:100%; border-collapse:collapse; margin: 6px 0 14px; }
  .metrics td { width:33%; border:1px solid #ECECEF; padding:11px; text-align:center; }
  .metric-label { color:#8a8a90; font-size:9.5px; text-transform:uppercase; letter-spacing:.04em; }
  .metric-value { font-size:18px; font-weight:bold; padding-top:4px; color:#0a0a0f; }
  .client { margin-top:12px; border:1px solid #ECECEF; border-radius:7px; padding:11px 13px; }
  .client-name { font-size:13px; font-weight:bold; }
  .client-meta { color:#8a8a90; font-size:9.5px; margin-bottom:6px; }
  .goal { margin:8px 0 0; padding-left:10px; border-left:2px solid #FCD82F; }
  .goal-title { font-weight:bold; font-size:12px; }
  .goal-sub { color:#8a8a90; font-size:9.5px; }
  .sec { font-size:11px; margin:3px 0 0; }
  .sec b { color:#56565c; }
  .empty { color:#8a8a90; font-style:italic; font-size:11px; }
@endsection

@section('content')
  <div class="pod-head"><span class="dot" style="background:{{ $pod->color }}"></span>{{ $pod->name }}</div>
  <div class="muted">Manager: {{ optional($pod->manager)->name ?? 'Unassigned' }}</div>

  <table class="metrics">
    <tr>
      <td><div class="metric-label">Clients</div><div class="metric-value">{{ $pod->clients->count() }}</div></td>
      <td><div class="metric-label">Goals</div><div class="metric-value">{{ $pod->clients->sum(fn($c) => $c->goals->count()) }}</div></td>
      <td><div class="metric-label">Tasks done ({{ $period['type'] }})</div><div class="metric-value">{{ $weeklyDone }}/{{ $weeklyTotal }}</div></td>
    </tr>
  </table>

  @forelse($pod->clients as $client)
    <div class="client">
      <div class="client-name">{{ $client->name }}</div>
      <div class="client-meta">{{ $client->industry }}</div>

      @forelse($client->goals as $goal)
        <div class="goal">
          <div class="goal-title">{{ $goal->title }}</div>
          <div class="goal-sub">Created {{ $goal->created_at->format('M Y') }}</div>
          @foreach(\App\Models\Goal::SECTION_TYPES as $type)
            @php $section = $goal->sections->firstWhere('type', $type); @endphp
            <div class="sec"><b>{{ ucfirst($type) }}:</b> {{ $section && $section->content ? $section->content : '—' }}</div>
          @endforeach
        </div>
      @empty
        <div class="empty">No goals for this client.</div>
      @endforelse
    </div>
  @empty
    <div class="empty">No clients in this pod yet.</div>
  @endforelse
@endsection
