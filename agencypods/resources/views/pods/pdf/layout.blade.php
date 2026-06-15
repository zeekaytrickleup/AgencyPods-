<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  @page { margin: 140px 42px 70px 42px; }
  * { font-family: DejaVu Sans, sans-serif; }
  body { color:#16161a; font-size:12px; }

  /* ===== Letterhead (repeats on every page) ===== */
  .letterhead { position: fixed; top: -112px; left: 0; right: 0; height: 96px; }
  .lh-table { width:100%; border-collapse:collapse; }
  .lh-badge { width:36px; height:36px; background:#0a0a0f; border-radius:8px; text-align:center; }
  .lh-badge img { width:17px; margin-top:9px; }
  .brandname { font-size:19px; font-weight:bold; color:#0a0a0f; padding-left:11px; letter-spacing:-0.3px; }
  .tagline { font-size:9.5px; color:#8a8a90; padding-top:5px; text-transform:uppercase; letter-spacing:1px; }
  .lh-right { text-align:right; vertical-align:middle; }
  .lh-title { font-size:14px; font-weight:bold; color:#0a0a0f; }
  .lh-sub { font-size:10px; color:#8a8a90; padding-top:2px; }
  .lh-rule { height:3px; background:#FCD82F; margin-top:9px; }
  .lh-rule2 { height:1px; background:#e6e6ea; }

  /* ===== Footer (repeats on every page) ===== */
  .footer { position: fixed; bottom: -42px; left:0; right:0; height:34px; }
  .footer-rule { height:1px; background:#e6e6ea; margin-bottom:6px; }
  .footer-table { width:100%; font-size:9px; color:#9a9aa0; border-collapse:collapse; }
  .pagenum:after { content: counter(page) " / " counter(pages); }

  /* ===== Watermark (repeats on every page, sits behind content) ===== */
  .watermark { position: fixed; top: 320px; left: 0; right: 0; text-align:center; }
  .watermark .wm { font-size:96px; font-weight:bold; color:#0a0a0f; opacity:0.045;
                   transform: rotate(-26deg); transform-origin: center; }

  @yield('styles')
</style>
</head>
<body>
  @php $logoData = 'data:image/svg+xml;base64,'.base64_encode(file_get_contents(public_path('logo.svg'))); @endphp
  {{-- Letterhead --}}
  <div class="letterhead">
    <table class="lh-table">
      <tr>
        <td style="vertical-align:middle; width:62%">
          <table><tr>
            <td class="lh-badge"><img src="{{ $logoData }}" alt="logo"></td>
            <td style="vertical-align:middle"><span class="brandname">{{ config('app.name') }}</span></td>
          </tr></table>
          <div class="tagline">Agency operations report</div>
        </td>
        <td class="lh-right">
          <div class="lh-title">@yield('doc-title')</div>
          <div class="lh-sub">@yield('doc-sub')</div>
          <div class="lh-sub">Generated {{ $generatedAt->format('j M Y, H:i') }}</div>
        </td>
      </tr>
    </table>
    <div class="lh-rule"></div>
    <div class="lh-rule2"></div>
  </div>

  {{-- Footer --}}
  <div class="footer">
    <div class="footer-rule"></div>
    <table class="footer-table">
      <tr>
        <td>{{ config('app.name') }} &middot; Confidential — for internal use only</td>
        <td style="text-align:right"><span class="pagenum"></span></td>
      </tr>
    </table>
  </div>

  {{-- Watermark --}}
  <div class="watermark"><span class="wm">{{ config('app.name') }}</span></div>

  {{-- Page body --}}
  <div class="doc-body">
    @yield('content')
  </div>
</body>
</html>
