<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ config('app.name') }} — @yield('title', 'Dashboard')</title>
<link rel="icon" type="image/svg+xml" href="/logo.svg">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.34.0/dist/tabler-icons.min.css">
<style>
:root{
  --brand:#FCD82F;--brand-soft:#FFF6D1;--brand-dark:#E8C620;--ink:#0a0a0f;
  --bg:#F5F5F7;--card:#FFFFFF;
  --border:#ECECEF;--border-strong:#DEDEE3;
  --t1:#0a0a0f;--t2:#56565c;--t3:#8A8A90;
  --shadow:0 1px 2px rgba(16,16,24,.04),0 4px 12px rgba(16,16,24,.05);
  --shadow-lg:0 8px 30px rgba(16,16,24,.10);
  --r-sm:8px;--r-md:10px;--r-lg:14px;
  /* legacy aliases still referenced inline by some views */
  --color-text-primary:#0a0a0f;--color-text-secondary:#56565c;--color-text-tertiary:#8A8A90;
  --color-background-primary:#FFFFFF;--color-background-secondary:#F5F5F7;
  --color-border-tertiary:#ECECEF;--color-border-secondary:#DEDEE3;--color-border-primary:#B9B9C0;
  --border-radius-md:10px;--border-radius-lg:14px;
}
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Helvetica,Arial,sans-serif;background:var(--bg);color:var(--t1);-webkit-font-smoothing:antialiased}
.app{display:flex;height:100vh;overflow:hidden}

/* ===== Sidebar (dark) ===== */
.sidebar{width:232px;flex-shrink:0;display:flex;flex-direction:column;background:linear-gradient(180deg,#16161d 0%,#0a0a0f 100%);color:#c9c9d1}
.sidebar-header{padding:16px;border-bottom:1px solid rgba(255,255,255,.07);display:flex;align-items:center;gap:11px}
.sidebar-logo{width:34px;height:34px;border-radius:9px;background:var(--ink);display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 4px 14px rgba(10,10,15,.25)}
.sidebar-logo img{width:17px;height:auto;display:block}
.sidebar-header p{font-size:10px;color:#7e7e8a;text-transform:uppercase;letter-spacing:.06em;margin-bottom:1px}
.sidebar-header h3{font-size:15px;font-weight:600;color:#fff;letter-spacing:-.01em}
.pod-list{flex:1;overflow-y:auto;padding:12px 8px}
.pod-list .nav-label{font-size:10px;color:#6c6c78;text-transform:uppercase;letter-spacing:.06em;padding:2px 10px 8px;font-weight:600}
.pod-item{position:relative;padding:9px 11px;border-radius:var(--r-sm);cursor:pointer;font-size:13px;color:#bdbdc7;display:flex;align-items:center;gap:9px;margin-bottom:3px;text-decoration:none;transition:background .12s,color .12s}
.pod-item:hover{background:rgba(255,255,255,.06);color:#fff}
.pod-item.active{background:rgba(255,255,255,.10);color:#fff;font-weight:600}
.pod-item.active::before{content:"";position:absolute;left:0;top:7px;bottom:7px;width:3px;border-radius:3px;background:var(--brand)}
.pod-dot{width:9px;height:9px;border-radius:50%;flex-shrink:0;box-shadow:0 0 0 2px rgba(255,255,255,.08)}
.sidebar-footer{padding:10px 8px;border-top:1px solid rgba(255,255,255,.07)}
.nav-btn{position:relative;width:100%;padding:9px 11px;border-radius:var(--r-sm);border:none;background:transparent;cursor:pointer;font-size:13px;color:#bdbdc7;display:flex;align-items:center;gap:9px;text-align:left;text-decoration:none;transition:background .12s,color .12s}
.nav-btn:hover{background:rgba(255,255,255,.06);color:#fff}
.nav-btn.active{background:rgba(255,255,255,.10);color:#fff;font-weight:600}
.nav-btn.active::before{content:"";position:absolute;left:0;top:7px;bottom:7px;width:3px;border-radius:3px;background:var(--brand)}

/* ===== Main ===== */
.main{flex:1;display:flex;flex-direction:column;overflow:hidden;background:var(--bg)}
.topbar{padding:0 24px;height:62px;flex-shrink:0;background:var(--card);border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between}
.topbar h2{font-size:18px;font-weight:600;color:var(--t1);letter-spacing:-.01em}
.topbar-right{display:flex;align-items:center;gap:14px}
.role-badge{font-size:11px;padding:4px 9px;border-radius:6px;background:var(--ink);color:var(--brand);font-weight:600}
.user-name{font-size:13px;color:var(--t2);font-weight:500}
.link-btn{background:none;border:none;color:var(--t3);font-size:12px;cursor:pointer;padding:0;display:inline-flex;align-items:center;gap:4px}
.link-btn:hover{color:var(--t1)}
.content{flex:1;overflow-y:auto;padding:22px 24px}
.section-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;gap:12px}
.section-title{font-size:11px;font-weight:600;color:var(--t3);text-transform:uppercase;letter-spacing:.06em}

/* ===== Buttons ===== */
.btn{padding:8px 13px;border-radius:var(--r-sm);border:1px solid var(--border-strong);background:var(--card);cursor:pointer;font-size:12.5px;font-weight:500;color:var(--t1);display:inline-flex;align-items:center;gap:6px;text-decoration:none;transition:background .12s,border-color .12s,box-shadow .12s}
.btn:hover{background:#fafafa;border-color:#cfcfd6}
.btn-primary{background:var(--brand);color:var(--ink);border-color:var(--brand);font-weight:700;box-shadow:0 2px 8px rgba(252,216,47,.35)}
.btn-primary:hover{background:var(--brand-dark);border-color:var(--brand-dark);box-shadow:0 4px 14px rgba(252,216,47,.45)}
.btn-danger{color:#B23B16;border-color:#EBC7BB;background:#fff}
.btn-danger:hover{background:#FBEEE9;border-color:#E0A892}

/* ===== Client cards ===== */
.client-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:8px}
.client-card{position:relative;background:var(--card);border:1px solid var(--border);border-radius:var(--r-lg);padding:16px 16px 14px;cursor:pointer;text-decoration:none;display:block;color:inherit;overflow:hidden;box-shadow:var(--shadow);transition:transform .12s,box-shadow .12s,border-color .12s}
.client-card::before{content:"";position:absolute;left:0;top:0;bottom:0;width:4px;background:var(--pod,var(--brand))}
.client-card:hover{transform:translateY(-2px);box-shadow:var(--shadow-lg);border-color:var(--border-strong)}
.client-card.selected{border-color:var(--ink);box-shadow:0 0 0 1.5px var(--ink),var(--shadow)}
.client-name{font-size:14px;font-weight:600;color:var(--t1);margin-bottom:3px}
.client-meta{font-size:11.5px;color:var(--t3);margin-bottom:12px}
.goal-count{display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:600;background:var(--brand-soft);padding:4px 9px;border-radius:20px;color:#7a6300}
.divider{height:1px;background:var(--border);margin:22px 0 18px}

/* ===== Goal cards ===== */
.goal-card{position:relative;background:var(--card);border:1px solid var(--border);border-radius:var(--r-lg);margin-bottom:12px;overflow:hidden;box-shadow:var(--shadow);transition:box-shadow .12s}
.goal-card:hover,.goal-card[open]{box-shadow:var(--shadow-lg)}
.goal-card>summary{padding:15px 16px;display:flex;align-items:center;justify-content:space-between;cursor:pointer;list-style:none;border-left:4px solid var(--pod,var(--brand))}
.goal-card>summary::-webkit-details-marker{display:none}
.goal-card>summary:hover{background:#fafafa}
.goal-title{font-size:14px;font-weight:600;color:var(--t1)}
.goal-subtitle{font-size:11.5px;color:var(--t3);margin-top:2px}
.chevron{transition:transform .2s;font-size:16px;color:var(--t2)}
.goal-card[open] .chevron{transform:rotate(180deg)}
.tabs{display:flex;gap:2px;border-bottom:1px solid var(--border);background:#fbfbfc;padding:0 10px}
.tab{padding:11px 14px;font-size:12.5px;cursor:pointer;color:var(--t2);border-bottom:2px solid transparent;margin-bottom:-1px;white-space:nowrap;background:none;border-top:none;border-left:none;border-right:none;font-weight:500;transition:color .12s}
.tab:hover{color:var(--t1)}
.tab.active{color:var(--ink);font-weight:600;border-bottom-color:var(--brand)}
.tab-body{background:var(--card);padding:16px}
.tab-body[hidden]{display:none}
.tab-text{font-size:13px;color:var(--t2);line-height:1.7;margin-bottom:14px;white-space:pre-line}
.tab-text.empty{color:var(--t3);font-style:italic}
.tab-toolbar{display:flex;gap:6px;margin-bottom:12px}
.attachments-label{font-size:11px;color:var(--t3);text-transform:uppercase;letter-spacing:.04em;margin-bottom:8px;font-weight:600}
.file-list{display:flex;flex-direction:column;gap:7px;margin-bottom:12px}
.file-item{display:flex;align-items:center;justify-content:space-between;padding:9px 11px;border:1px solid var(--border);border-radius:var(--r-sm);background:#fbfbfc;transition:border-color .12s}
.file-item:hover{border-color:var(--border-strong)}
.file-left{display:flex;align-items:center;gap:10px}
.file-icon{width:30px;height:30px;border-radius:7px;display:flex;align-items:center;justify-content:center;font-size:15px;flex-shrink:0}
.file-icon.pdf{background:#FBE9E3;color:#C0431E}
.file-icon.img{background:#DFF5EE;color:#0F6E56}
.file-icon.doc{background:#E4EFFB;color:#185FA5}
.file-icon.xls{background:#E9F4DC;color:#3B6D11}
.file-name{font-size:12.5px;font-weight:500;color:var(--t1)}
.file-size{font-size:11px;color:var(--t3)}
.file-actions{display:flex;gap:4px;align-items:center}
.file-actions form{display:flex}
.upload-zone{display:block;border:1.5px dashed var(--border-strong);border-radius:var(--r-sm);padding:14px;text-align:center;color:var(--t3);font-size:12px;cursor:pointer;transition:.12s}
.upload-zone:hover{border-color:var(--brand-dark);color:#7a6300;background:var(--brand-soft)}

/* ===== Weekly table ===== */
.weekly-table{width:100%;border-collapse:collapse;font-size:13px;background:var(--card);border:1px solid var(--border);border-radius:var(--r-lg);overflow:hidden;box-shadow:var(--shadow)}
.weekly-table th{text-align:left;padding:11px 14px;color:var(--t3);font-weight:600;background:#fbfbfc;border-bottom:1px solid var(--border);font-size:11px;text-transform:uppercase;letter-spacing:.04em}
.weekly-table td{padding:12px 14px;border-bottom:1px solid var(--border);color:var(--t1)}
.weekly-table tr:last-child td{border-bottom:none}
.weekly-table tbody tr:hover{background:#fafafa}
.status-pill{display:inline-flex;align-items:center;gap:6px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600}
.status-pill::before{content:"";width:6px;height:6px;border-radius:50%}
.status-done{background:#E9F4DC;color:#3B6D11}
.status-done::before{background:#5C9A1B}
.status-pending{background:#FCEFD6;color:#8a5a08}
.status-pending::before{background:#D69220}
.icon-btn{background:none;border:none;cursor:pointer;color:var(--t3);padding:5px;border-radius:6px;font-size:15px;display:inline-flex;align-items:center;transition:.12s}
.icon-btn:hover{color:var(--ink);background:var(--brand-soft)}

/* ===== Metric cards ===== */
.metric-card{position:relative;background:var(--card);border:1px solid var(--border);border-radius:var(--r-lg);padding:16px 18px;box-shadow:var(--shadow);overflow:hidden}
.metric-card::before{content:"";position:absolute;left:0;top:0;bottom:0;width:4px;background:var(--brand)}
.metric-label{font-size:11px;color:var(--t3);margin-bottom:6px;text-transform:uppercase;letter-spacing:.04em;font-weight:600}
.metric-value{font-size:26px;font-weight:700;color:var(--t1);letter-spacing:-.02em}

.empty-state{text-align:center;padding:48px 32px;color:var(--t3);font-size:13px;background:var(--card);border:1px dashed var(--border-strong);border-radius:var(--r-lg)}
.flash{margin:14px 24px 0;padding:10px 14px;border-radius:var(--r-sm);background:#E9F4DC;color:#3B6D11;font-size:12.5px;font-weight:500;border:1px solid #CFE6B0}

/* ===== Dialogs ===== */
dialog{margin:auto;border:1px solid var(--border-strong);border-radius:var(--r-lg);padding:0;width:380px;max-width:92vw;box-shadow:0 24px 60px rgba(10,10,15,.30)}
dialog::backdrop{background:rgba(10,10,15,.45)}
.dlg-body{padding:20px}
.dlg-title{font-size:15px;font-weight:600;margin-bottom:16px}
.field{margin-bottom:13px}
.field label{display:block;font-size:11px;color:var(--t3);text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px;font-weight:600}
.field input,.field textarea,.field select{width:100%;padding:9px 11px;border:1px solid var(--border-strong);border-radius:var(--r-sm);font-size:13.5px;font-family:inherit;color:var(--t1);background:#fff;transition:border-color .12s,box-shadow .12s}
.field input:focus,.field textarea:focus,.field select:focus{outline:none;border-color:var(--brand-dark);box-shadow:0 0 0 3px rgba(252,216,47,.3)}
.field textarea{min-height:96px;resize:vertical;line-height:1.6}
.dlg-actions{display:flex;justify-content:flex-end;gap:8px;margin-top:8px}
</style>
@stack('head')
</head>
<body>
<div class="app">
  <div class="sidebar">
    <div class="sidebar-header">
      <div class="sidebar-logo"><img src="/logo.svg" alt="{{ config('app.name') }}"></div>
      <div>
        <p>Workspace</p>
        <h3>{{ config('app.name') }}</h3>
      </div>
    </div>
    <div class="pod-list">
      <div class="nav-label">Pods</div>
      @forelse($navPods as $p)
        @php $isActive = request()->routeIs('dashboard') && (int) request('pod', $navPods->first()->id) === $p->id; @endphp
        <a class="pod-item {{ $isActive ? 'active' : '' }}" href="{{ route('dashboard', ['pod' => $p->id]) }}">
          <span class="pod-dot" style="background:{{ $p->color }}"></span>{{ $p->name }}
        </a>
      @empty
        <div style="padding:10px;font-size:12px;color:#7e7e8a">No pods assigned.</div>
      @endforelse
    </div>
    <div class="sidebar-footer">
      <a class="nav-btn {{ request()->routeIs('weekly.index') ? 'active' : '' }}" href="{{ route('weekly.index') }}"><i class="ti ti-calendar" aria-hidden="true"></i> Weekly goals</a>
      @if(auth()->user()->isSuperAdmin())
        <a class="nav-btn {{ request()->routeIs('reports.index') ? 'active' : '' }}" href="{{ route('reports.index') }}"><i class="ti ti-chart-bar" aria-hidden="true"></i> Reports</a>
        <a class="nav-btn {{ request()->routeIs('users.index') ? 'active' : '' }}" href="{{ route('users.index') }}"><i class="ti ti-users-group" aria-hidden="true"></i> Users</a>
      @endif
      @if(auth()->user()->isPodManager())
        <a class="nav-btn {{ request()->routeIs('team.index') ? 'active' : '' }}" href="{{ route('team.index') }}"><i class="ti ti-users" aria-hidden="true"></i> My team</a>
      @endif
    </div>
  </div>
  <div class="main">
    <div class="topbar">
      <h2>@yield('title', 'Dashboard')</h2>
      <div class="topbar-right">
        <span class="role-badge">{{ auth()->user()->roleLabel() }}</span>
        <span class="user-name">{{ auth()->user()->name }}</span>
        <form method="POST" action="{{ route('logout') }}">@csrf
          <button type="submit" class="link-btn"><i class="ti ti-logout" aria-hidden="true"></i> Log out</button>
        </form>
      </div>
    </div>
    @if(session('status'))
      <div class="flash">{{ session('status') }}</div>
    @endif
    @if($errors->any())
      <div class="flash" style="background:#FBEEE9;color:#B23B16;border-color:#EBC7BB">{{ $errors->first() }}</div>
    @endif
    <div class="content">
      @yield('content')
    </div>
  </div>
</div>
@stack('scripts')
</body>
</html>
