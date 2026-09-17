<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Action not allowed | {{ config('travels.brand.name', 'Travels') }}</title>
<style>
*{box-sizing:border-box}html,body{margin:0;min-height:100%;font-family:Inter,ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif}
body{background:#f8fafc;color:#17202b;display:flex;align-items:center;justify-content:center;padding:32px}
.error-shell{width:min(720px,100%);text-align:center}
.error-card{background:#fff;border:1px solid #e5e7eb;border-radius:20px;padding:48px 42px;box-shadow:0 18px 60px rgba(15,23,42,.08)}
.error-code{font-size:76px;line-height:1;font-weight:800;letter-spacing:-.06em;color:#d97706;margin-bottom:18px}
.error-title{font-size:30px;line-height:1.2;margin:0 0 12px;font-weight:750}
.error-message{font-size:16px;line-height:1.7;color:#667085;margin:0 auto 28px;max-width:560px}
.error-actions{display:flex;align-items:center;justify-content:center;gap:10px;flex-wrap:wrap}
.error-button{display:inline-flex;align-items:center;justify-content:center;min-height:44px;padding:0 18px;border-radius:10px;background:#d97706;color:#fff;text-decoration:none;font-weight:700;border:1px solid #d97706}
.error-button:hover{background:#b45309;border-color:#b45309}
.error-button.secondary{background:#fff;color:#17202b;border-color:#d1d5db}
.error-brand{margin-top:18px;color:#98a2b3;font-size:12px}
@media(max-width:560px){body{padding:18px}.error-card{padding:36px 22px}.error-code{font-size:60px}.error-title{font-size:24px}.error-message{font-size:14px}}
</style>
</head>
<body>
<main class="error-shell">
<div class="error-card">
<div class="error-code">405</div>
<h1 class="error-title">Action not allowed</h1>
<p class="error-message">That action isn't available for this address.</p>
<div class="error-actions">
<a class="error-button" href="{{ url('/') }}">Go to Homepage</a>
<a class="error-button secondary" href="javascript:history.back()">Go Back</a>
</div>
</div>
<div class="error-brand">{{ config('travels.brand.name', 'Travels') }}</div>
</main>
</body>
</html>