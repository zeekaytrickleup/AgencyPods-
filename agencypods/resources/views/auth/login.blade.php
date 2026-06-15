<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Sign in — {{ config('app.name') }}</title>
<link rel="icon" type="image/svg+xml" href="/logo.svg">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.34.0/dist/tabler-icons.min.css">
<style>
:root{
  --brand:#FCD82F;--brand-dark:#E8C620;--ink:#0a0a0f;
  --bg:#F4F4F6;--card:#FFFFFF;--border:#E6E6EA;--border-strong:#D6D6DC;
  --text:#0a0a0f;--text-2:#5A5A57;--text-3:#8A8A85;
  --danger-bg:#FAECE7;--danger-text:#993C1D;--ok-bg:#EAF3DE;--ok-text:#3B6D11;
  --radius:10px;
}
*{box-sizing:border-box;margin:0;padding:0}
body{
  font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Helvetica,Arial,sans-serif;
  color:var(--text);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px;
  background:radial-gradient(1200px 600px at 50% -10%, #1a1a22 0%, #0a0a0f 60%);
}
.wrap{width:100%;max-width:400px}
.brand{display:flex;flex-direction:column;align-items:center;margin-bottom:22px}
.logo-badge{width:56px;height:56px;border-radius:14px;background:var(--ink);display:flex;align-items:center;justify-content:center;margin-bottom:14px;box-shadow:0 6px 20px rgba(0,0,0,0.35);border:1px solid #23232b}
.logo-badge img{width:26px;height:auto;display:block}
.brand h1{font-size:21px;font-weight:600;letter-spacing:-0.01em;color:#fff}
.brand p{font-size:13px;color:#9a9aa2;margin-top:3px}
.card{background:var(--card);border:0.5px solid var(--border);border-radius:14px;padding:26px 26px 24px;box-shadow:0 18px 50px rgba(0,0,0,0.35)}
.alert{padding:9px 12px;border-radius:8px;font-size:12.5px;margin-bottom:16px;display:flex;align-items:center;gap:7px}
.alert.err{background:var(--danger-bg);color:var(--danger-text)}
.alert.ok{background:var(--ok-bg);color:var(--ok-text)}
.field{margin-bottom:15px}
.field label{display:block;font-size:12px;font-weight:500;color:var(--text-2);margin-bottom:6px}
.field input[type=email],.field input[type=password]{
  width:100%;padding:10px 12px;border:1px solid var(--border-strong);border-radius:var(--radius);
  font-size:14px;font-family:inherit;color:var(--text);background:#FCFCFD;transition:border-color .15s, box-shadow .15s;
}
.field input:focus{outline:none;border-color:var(--brand-dark);box-shadow:0 0 0 3px rgba(252,216,47,0.35);background:#fff}
.row{display:flex;align-items:center;justify-content:space-between;margin:6px 0 18px}
.remember{display:flex;align-items:center;gap:7px;font-size:13px;color:var(--text-2);cursor:pointer}
.remember input{width:15px;height:15px;accent-color:var(--ink)}
.forgot{font-size:13px;color:var(--text);text-decoration:none;border-bottom:1px solid var(--brand)}
.forgot:hover{color:var(--brand-dark);border-bottom-color:var(--brand-dark)}
.btn-primary{
  width:100%;padding:12px;border:none;border-radius:var(--radius);background:var(--ink);color:var(--brand);
  font-size:14px;font-weight:700;cursor:pointer;transition:transform .05s, box-shadow .15s;
}
.btn-primary:hover{box-shadow:0 6px 18px rgba(10,10,15,0.35)}
.btn-primary:active{transform:translateY(1px)}
.demo{margin-top:18px}
.demo-divider{display:flex;align-items:center;gap:10px;color:var(--text-3);font-size:11px;text-transform:uppercase;letter-spacing:0.06em;margin-bottom:12px}
.demo-divider::before,.demo-divider::after{content:"";flex:1;height:1px;background:var(--border)}
.acct{
  display:flex;align-items:center;justify-content:space-between;gap:10px;width:100%;text-align:left;
  border:0.5px solid var(--border);border-radius:var(--radius);padding:10px 12px;margin-bottom:8px;background:#FAFAFB;cursor:pointer;
  transition:border-color .15s, background .15s;
}
.acct:hover{border-color:var(--brand-dark);background:#fff}
.acct-info{display:flex;align-items:center;gap:10px;min-width:0}
.acct-badge{flex-shrink:0;width:30px;height:30px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:15px}
.acct-text{min-width:0}
.acct-role{font-size:12.5px;font-weight:600;color:var(--text)}
.acct-mail{font-size:11.5px;color:var(--text-3);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.acct-use{flex-shrink:0;font-size:11px;font-weight:700;color:var(--ink);background:var(--brand);border:none;border-radius:6px;padding:5px 11px}
.demo-hint{font-size:11px;color:var(--text-3);text-align:center;margin-top:4px}
.footer{text-align:center;font-size:11.5px;color:#7a7a82;margin-top:18px}
</style>
</head>
<body>
<div class="wrap">
  <div class="brand">
    <div class="logo-badge"><img src="/logo.svg" alt="{{ config('app.name') }}"></div>
    <h1>{{ config('app.name') }}</h1>
    <p>Sign in to your workspace</p>
  </div>

  <div class="card">
    @if(session('status'))
      <div class="alert ok"><i class="ti ti-circle-check" aria-hidden="true"></i>{{ session('status') }}</div>
    @endif
    @if($errors->any())
      <div class="alert err"><i class="ti ti-alert-circle" aria-hidden="true"></i>{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}" id="loginForm">
      @csrf
      <div class="field">
        <label for="email">Email</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="you@trickleup.com">
      </div>
      <div class="field">
        <label for="password">Password</label>
        <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••">
      </div>
      <div class="row">
        <label class="remember"><input type="checkbox" name="remember"> Remember me</label>
        @if(Route::has('password.request'))
          <a class="forgot" href="{{ route('password.request') }}">Forgot password?</a>
        @endif
      </div>
      <button type="submit" class="btn-primary">Log in</button>
    </form>

    <div class="demo">
      <div class="demo-divider">Demo accounts</div>

      <button type="button" class="acct" onclick="fillLogin('admin@agencypods.test')">
        <span class="acct-info">
          <span class="acct-badge" style="background:#0a0a0f;color:#FCD82F"><i class="ti ti-shield-check" aria-hidden="true"></i></span>
          <span class="acct-text">
            <span class="acct-role">Super Admin</span>
            <span class="acct-mail">admin@agencypods.test</span>
          </span>
        </span>
        <span class="acct-use">Use</span>
      </button>

      <button type="button" class="acct" onclick="fillLogin('manager1@agencypods.test')">
        <span class="acct-info">
          <span class="acct-badge" style="background:#FCD82F;color:#0a0a0f"><i class="ti ti-user" aria-hidden="true"></i></span>
          <span class="acct-text">
            <span class="acct-role">Pod Manager</span>
            <span class="acct-mail">manager1@agencypods.test</span>
          </span>
        </span>
        <span class="acct-use">Use</span>
      </button>

      <div class="demo-hint">Password for both: <strong>password</strong></div>
    </div>
  </div>

  <div class="footer">{{ config('app.name') }} · Internal agency dashboard</div>
</div>

<script>
function fillLogin(email) {
    document.getElementById('email').value = email;
    document.getElementById('password').value = 'password';
    document.getElementById('password').focus();
}
</script>
</body>
</html>
