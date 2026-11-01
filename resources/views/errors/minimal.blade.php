<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - SIAP AUM</title>
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:500,600,700|bricolage-grotesque:700,800" rel="stylesheet">
    <style>
        *{box-sizing:border-box}
        body{margin:0;min-height:100vh;display:grid;place-items:center;padding:24px;font-family:'Plus Jakarta Sans',sans-serif;color:#16161b;background:#f7f1f0;background-image:radial-gradient(900px 520px at -5% -10%,rgba(255,196,184,.55),transparent 60%),radial-gradient(800px 520px at 105% 0%,rgba(255,214,206,.55),transparent 58%)}
        .card{position:relative;width:min(560px,100%);padding:48px 40px;overflow:hidden;border-radius:36px;text-align:center;background:rgba(255,255,255,.7);backdrop-filter:blur(18px);border:1px solid rgba(255,255,255,.8);box-shadow:0 30px 60px -24px rgba(120,20,10,.28)}
        .code{font-family:'Bricolage Grotesque',sans-serif;font-size:120px;font-weight:800;line-height:.9;letter-spacing:-.06em;color:transparent;background:linear-gradient(150deg,#ec4a35,#8f1d14);-webkit-background-clip:text;background-clip:text}
        .msg{margin:18px 0 8px;font-size:22px;font-weight:700}
        p{margin:0 0 28px;color:#74747f}
        a{display:inline-flex;align-items:center;gap:8px;height:50px;padding:0 24px;border-radius:999px;color:#fff;background:#d92d20;font-weight:700;text-decoration:none;box-shadow:0 16px 30px -14px rgba(217,45,32,.95);transition:transform .2s ease,background-color .2s ease}
        a:hover{background:#b42318;transform:translateY(-2px)}
    </style>
</head>
<body>
    <main class="card">
        <div class="code">@yield('code')</div>
        <div class="msg">@yield('message')</div>
        <p>Halaman yang Anda tuju tidak dapat ditampilkan saat ini.</p>
        <a href="{{ url('/') }}">Kembali ke Beranda</a>
    </main>
</body>
</html>
