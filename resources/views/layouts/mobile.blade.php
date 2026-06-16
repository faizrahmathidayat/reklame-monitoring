<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>(function(){var t=localStorage.getItem('reklame-theme');if(t==='light')document.documentElement.setAttribute('data-theme','light');})();</script>
    <title>@yield('title', 'Dashboard') — Reklame</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* ── CSS Variables (same as desktop) ── */
        :root {
            --bg-base:      #0f172a;
            --bg-surface:   #1e293b;
            --bg-elevated:  #263348;
            --border-color: rgba(99,102,241,0.18);
            --text-primary: #f1f5f9;
            --text-muted:   #94a3b8;
            --text-dim:     #64748b;
            --accent:       #6366f1;
            --accent-soft:  rgba(99,102,241,0.15);
            --accent-hover: rgba(99,102,241,0.25);
            --topbar-bg:    rgba(30,41,59,0.95);
            --danger:       #ef4444;
            --success:      #22c55e;
            --warning:      #f59e0b;
        }
        html[data-theme="light"] {
            --bg-base:      #f8f9ff;
            --bg-surface:   #ffffff;
            --bg-elevated:  #eef0ff;
            --border-color: rgba(99,102,241,0.18);
            --text-primary: #1e293b;
            --text-muted:   #475569;
            --text-dim:     #94a3b8;
            --accent-soft:  rgba(99,102,241,0.1);
            --accent-hover: rgba(99,102,241,0.12);
            --topbar-bg:    rgba(255,255,255,0.97);
        }

        * { font-family: 'Inter', sans-serif; box-sizing: border-box; }

        body {
            background: var(--bg-base);
            color: var(--text-primary);
            margin: 0;
            min-height: 100vh;
            padding-top: 56px;
            padding-bottom: 68px;
        }

        /* ── Top Bar ── */
        .m-topbar {
            position: fixed;
            top: 0; left: 0; right: 0;
            height: 56px;
            background: var(--topbar-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            padding: 0 14px;
            gap: 10px;
            z-index: 100;
        }
        .m-topbar-brand {
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }
        .m-topbar-icon {
            width: 32px; height: 32px;
            background: linear-gradient(135deg, var(--accent), #8b5cf6);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.8rem; color: white;
            flex-shrink: 0;
        }
        .m-topbar-title {
            flex: 1;
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-primary);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .m-topbar-btn {
            background: none;
            border: 1px solid var(--border-color);
            color: var(--text-dim);
            width: 32px; height: 32px;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            font-size: 0.8rem;
            flex-shrink: 0;
            text-decoration: none;
        }

        /* ── Bottom Navigation ── */
        .m-bottom-nav {
            position: fixed;
            bottom: 0; left: 0; right: 0;
            height: 60px;
            background: var(--bg-surface);
            border-top: 1px solid var(--border-color);
            display: flex;
            align-items: stretch;
            z-index: 100;
        }
        .m-nav-item {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 3px;
            color: var(--text-dim);
            text-decoration: none;
            font-size: 0.6rem;
            font-weight: 500;
            padding: 6px 2px 4px;
            transition: color 0.15s;
        }
        .m-nav-item i { font-size: 1.1rem; }
        .m-nav-item.active { color: var(--accent); }
        .m-nav-item:active { opacity: 0.7; }

        /* ── Main Content ── */
        .m-content {
            padding: 14px;
        }

        /* ── Section Title ── */
        .m-section-title {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            color: var(--text-dim);
            margin: 16px 0 8px;
        }

        /* ── Mobile Card ── */
        .m-card {
            background: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 12px 14px;
        }
        .m-card + .m-card { margin-top: 8px; }

        /* ── Stat Card ── */
        .stat-card {
            background: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 14px;
        }
        .stat-icon {
            width: 38px; height: 38px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem;
            margin-bottom: 8px;
        }
        .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-primary);
            line-height: 1;
            margin-bottom: 3px;
        }
        .stat-label {
            font-size: 0.7rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        /* ── Card Dark (shared with desktop) ── */
        .card-dark {
            background: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: 12px;
        }
        .card-dark .card-header {
            background: transparent;
            border-bottom: 1px solid var(--border-color);
            padding: 12px 14px;
        }
        .card-dark .card-body { padding: 14px; }
        .card-dark .card-title {
            color: var(--text-primary);
            font-size: 0.875rem;
            font-weight: 600;
            margin: 0;
        }

        /* ── Status Badges ── */
        .status-badge {
            display: inline-block;
            padding: 0.18rem 0.55rem;
            border-radius: 0.3rem;
            font-size: 0.65rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            white-space: nowrap;
        }
        .status-cancel           { background: rgba(239,68,68,0.15);   color: #fca5a5; }
        .status-input-web        { background: rgba(59,130,246,0.15);  color: #93c5fd; }
        .status-pemberkasan      { background: rgba(245,158,11,0.15);  color: #fcd34d; }
        .status-petugas          { background: rgba(168,85,247,0.15);  color: #d8b4fe; }
        .status-menunggu-ipr     { background: rgba(234,179,8,0.15);   color: #fef08a; }
        .status-proses-invoice   { background: rgba(14,165,233,0.15);  color: #7dd3fc; }
        .status-proses-bayar     { background: rgba(249,115,22,0.15);  color: #fdba74; }
        .status-inv-terkirim     { background: rgba(20,184,166,0.15);  color: #5eead4; }
        .status-selesai          { background: rgba(34,197,94,0.15);   color: #86efac; }
        .status-selesai-skpd     { background: rgba(52,211,153,0.15);  color: #6ee7b7; }

        /* ── Role Badge ── */
        .role-badge {
            display: inline-block;
            padding: 0.12rem 0.45rem;
            border-radius: 0.3rem;
            font-size: 0.6rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .role-badge.superadmin { background: rgba(99,102,241,0.2);  color: #a5b4fc; }
        .role-badge.staff      { background: rgba(34,197,94,0.15);  color: #86efac; }
        .role-badge.finance    { background: rgba(245,158,11,0.15); color: #fcd34d; }

        /* ── Form Controls ── */
        .form-control, .form-select {
            background: var(--bg-base);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            border-radius: 8px;
            font-size: 0.9rem;
        }
        .form-control:focus, .form-select:focus {
            background: var(--bg-base);
            border-color: var(--accent);
            color: var(--text-primary);
            box-shadow: 0 0 0 3px rgba(99,102,241,0.2);
        }
        .form-control::placeholder { color: var(--text-dim); }
        .form-label {
            color: var(--text-muted);
            font-size: 0.8rem;
            font-weight: 500;
            margin-bottom: 4px;
        }
        .input-group-text {
            background: var(--bg-elevated);
            border: 1px solid var(--border-color);
            color: var(--text-dim);
        }
        .form-select option { background: var(--bg-surface); }

        /* ── Buttons ── */
        .btn-primary {
            background: linear-gradient(135deg, var(--accent), #8b5cf6);
            border: none;
            box-shadow: 0 4px 12px rgba(99,102,241,0.3);
        }
        .btn-primary:hover { opacity: 0.9; }
        .btn-outline-secondary {
            border-color: var(--border-color);
            color: var(--text-muted);
        }
        .btn-outline-secondary:hover {
            background: var(--accent-hover);
            border-color: var(--accent);
            color: var(--text-primary);
        }

        /* ── Alert ── */
        .alert { border-radius: 10px; font-size: 0.85rem; }

        /* ── Pagination ── */
        .pagination { gap: 3px; }
        .pagination .page-link {
            background: var(--bg-surface);
            border-color: var(--border-color);
            color: var(--text-muted);
            border-radius: 6px !important;
            font-size: 0.78rem;
            padding: 0.28rem 0.55rem;
        }
        .pagination .page-link:hover {
            background: var(--accent-hover);
            border-color: var(--accent);
            color: var(--text-primary);
        }
        .pagination .active .page-link {
            background: var(--accent);
            border-color: var(--accent);
            color: white;
        }
        .pagination .disabled .page-link {
            background: var(--bg-base);
            border-color: var(--border-color);
            color: var(--text-dim);
        }

        /* ── FAB ── */
        .m-fab {
            position: fixed;
            bottom: 72px;
            right: 16px;
            width: 52px; height: 52px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent), #8b5cf6);
            border: none;
            color: white;
            font-size: 1.2rem;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 4px 16px rgba(99,102,241,0.5);
            text-decoration: none;
            z-index: 90;
        }
        .m-fab:active { transform: scale(0.93); color: white; }

        /* ── Light Mode ── */
        html[data-theme="light"] .status-cancel         { background: rgba(239,68,68,0.1);   color: #dc2626; }
        html[data-theme="light"] .status-input-web      { background: rgba(59,130,246,0.1);  color: #2563eb; }
        html[data-theme="light"] .status-pemberkasan    { background: rgba(245,158,11,0.1);  color: #d97706; }
        html[data-theme="light"] .status-petugas        { background: rgba(168,85,247,0.1);  color: #7c3aed; }
        html[data-theme="light"] .status-menunggu-ipr   { background: rgba(234,179,8,0.1);   color: #b45309; }
        html[data-theme="light"] .status-proses-invoice { background: rgba(14,165,233,0.1);  color: #0369a1; }
        html[data-theme="light"] .status-proses-bayar   { background: rgba(249,115,22,0.1);  color: #c2410c; }
        html[data-theme="light"] .status-inv-terkirim   { background: rgba(20,184,166,0.1);  color: #0f766e; }
        html[data-theme="light"] .status-selesai        { background: rgba(34,197,94,0.1);   color: #16a34a; }
        html[data-theme="light"] .status-selesai-skpd   { background: rgba(52,211,153,0.1);  color: #059669; }
        html[data-theme="light"] .role-badge.superadmin { background: rgba(99,102,241,0.12); color: #4338ca; }
        html[data-theme="light"] .role-badge.staff      { background: rgba(34,197,94,0.12);  color: #16a34a; }
        html[data-theme="light"] .role-badge.finance    { background: rgba(245,158,11,0.12); color: #d97706; }

        @stack('styles')
    </style>
</head>
<body>

{{-- ── Top Bar ── --}}
<header class="m-topbar">
    <a href="{{ route('dashboard') }}" class="m-topbar-brand">
        <div class="m-topbar-icon">
            <i class="fa-solid fa-rectangle-ad"></i>
        </div>
    </a>
    <div class="m-topbar-title">@yield('page-title', 'Dashboard')</div>
    <button id="themeToggle" class="m-topbar-btn" title="Ganti Tema">
        <i id="themeIcon" class="fa-solid fa-sun"></i>
    </button>
    <form method="POST" action="{{ route('logout') }}" class="d-flex">
        @csrf
        <button type="submit" class="m-topbar-btn" title="Logout">
            <i class="fa-solid fa-right-from-bracket"></i>
        </button>
    </form>
</header>

{{-- Flash Messages --}}
@if(session('success'))
<div style="padding: 10px 14px 0">
    <div class="alert d-flex align-items-center gap-2" role="alert"
         style="background:rgba(34,197,94,0.1);border:1px solid rgba(34,197,94,0.3);color:#86efac;padding:0.5rem 0.75rem;margin:0">
        <i class="fa-solid fa-circle-check"></i>
        <span style="font-size:0.82rem">{{ session('success') }}</span>
    </div>
</div>
@endif
@if(session('error'))
<div style="padding: 10px 14px 0">
    <div class="alert d-flex align-items-center gap-2" role="alert"
         style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);color:#fca5a5;padding:0.5rem 0.75rem;margin:0">
        <i class="fa-solid fa-circle-exclamation"></i>
        <span style="font-size:0.82rem">{{ session('error') }}</span>
    </div>
</div>
@endif

{{-- Content --}}
<main class="m-content">
    @yield('content')
</main>

{{-- ── Bottom Navigation ── --}}
<nav class="m-bottom-nav">
    <a href="{{ route('dashboard') }}"
       class="m-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <i class="fa-solid fa-gauge"></i>
        <span>Dashboard</span>
    </a>
    <a href="{{ route('spk.index') }}"
       class="m-nav-item {{ request()->routeIs('spk.*') ? 'active' : '' }}">
        <i class="fa-solid fa-file-contract"></i>
        <span>SPK</span>
    </a>
    <a href="{{ route('reklame.index') }}"
       class="m-nav-item {{ request()->routeIs('reklame.*') ? 'active' : '' }}">
        <i class="fa-solid fa-layer-group"></i>
        <span>Reklame</span>
    </a>
    <a href="{{ route('report') }}"
       class="m-nav-item {{ request()->routeIs('report*') ? 'active' : '' }}">
        <i class="fa-solid fa-chart-bar"></i>
        <span>Laporan</span>
    </a>
    @if(auth()->check() && auth()->user()->hasRole(['superadmin','staff']))
    <button type="button" id="btnMoreMenu"
       class="m-nav-item border-0 {{ request()->routeIs('master.*') || request()->routeIs('users.*') ? 'active' : '' }}"
       style="background:none;cursor:pointer">
        <i class="fa-solid fa-ellipsis"></i>
        <span>Lainnya</span>
    </button>
    @endif
</nav>

{{-- ── More Menu Bottom Sheet ── --}}
@if(auth()->check() && auth()->user()->hasRole(['superadmin','staff']))
<div id="moreMenuOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:200" onclick="closeMoreMenu()"></div>
<div id="moreMenuSheet" style="display:none;position:fixed;bottom:0;left:0;right:0;background:var(--bg-surface);border-radius:16px 16px 0 0;border-top:1px solid var(--border-color);z-index:201;padding:16px 16px 32px;transform:translateY(100%);transition:transform 0.25s ease">
    <div style="width:36px;height:4px;background:var(--border-color);border-radius:2px;margin:0 auto 16px"></div>
    <div style="font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:var(--text-dim);margin-bottom:10px">
        <i class="fa-solid fa-database me-1"></i>Master Data
    </div>
    <div class="row g-2 mb-3">
        <div class="col-6">
            <a href="{{ route('master.wilayah.index') }}" class="m-more-item {{ request()->routeIs('master.wilayah.*') ? 'active' : '' }}">
                <i class="fa-solid fa-map-location-dot"></i>
                <span>Wilayah / DC</span>
            </a>
        </div>
        <div class="col-6">
            <a href="{{ route('master.brand.index') }}" class="m-more-item {{ request()->routeIs('master.brand.*') ? 'active' : '' }}">
                <i class="fa-solid fa-tag"></i>
                <span>Brand</span>
            </a>
        </div>
        <div class="col-6">
            <a href="{{ route('master.toko.index') }}" class="m-more-item {{ request()->routeIs('master.toko.*') ? 'active' : '' }}">
                <i class="fa-solid fa-store"></i>
                <span>Toko</span>
            </a>
        </div>
        <div class="col-6">
            <a href="{{ route('master.cabang.index') }}" class="m-more-item {{ request()->routeIs('master.cabang.*') ? 'active' : '' }}">
                <i class="fa-solid fa-code-branch"></i>
                <span>Cabang</span>
            </a>
        </div>
        <div class="col-6">
            <a href="{{ route('master.pic.index') }}" class="m-more-item {{ request()->routeIs('master.pic.*') ? 'active' : '' }}">
                <i class="fa-solid fa-user-tie"></i>
                <span>PIC</span>
            </a>
        </div>
        @if(auth()->user()->isSuperadmin())
        <div class="col-6">
            <a href="{{ route('users.index') }}" class="m-more-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="fa-solid fa-users-gear"></i>
                <span>Users</span>
            </a>
        </div>
        @endif
    </div>
    <a href="?view=desktop" style="display:flex;align-items:center;gap:8px;font-size:0.78rem;color:var(--text-dim);text-decoration:none;padding:8px 4px;border-top:1px solid var(--border-color);padding-top:12px">
        <i class="fa-solid fa-desktop"></i> Versi Desktop
    </a>
</div>
<style>
.m-more-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 12px;
    background: var(--bg-elevated);
    border: 1px solid var(--border-color);
    border-radius: 10px;
    color: var(--text-muted);
    text-decoration: none;
    font-size: 0.8rem;
    font-weight: 500;
}
.m-more-item i { font-size: 0.9rem; color: var(--accent); }
.m-more-item.active { border-color: var(--accent); color: var(--accent); background: var(--accent-soft); }
</style>
@else
{{-- Link ke Desktop View (untuk non-superadmin/staff) --}}
<div style="text-align:center;padding:4px 0 2px;position:fixed;bottom:60px;left:0;right:0;z-index:89">
    <a href="?view=desktop" style="font-size:0.65rem;color:var(--text-dim);text-decoration:none;opacity:0.6">
        <i class="fa-solid fa-desktop me-1"></i>Versi Desktop
    </a>
</div>
@endif

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    var themeToggle = document.getElementById('themeToggle');
    var themeIcon   = document.getElementById('themeIcon');

    function applyTheme(t) {
        if (t === 'light') {
            document.documentElement.setAttribute('data-theme', 'light');
            themeIcon.className = 'fa-solid fa-moon';
        } else {
            document.documentElement.removeAttribute('data-theme');
            themeIcon.className = 'fa-solid fa-sun';
        }
    }
    applyTheme(localStorage.getItem('reklame-theme') || 'dark');

    themeToggle.addEventListener('click', function () {
        var next = document.documentElement.getAttribute('data-theme') === 'light' ? 'dark' : 'light';
        localStorage.setItem('reklame-theme', next);
        applyTheme(next);
    });
</script>

@stack('scripts')

@if(auth()->check() && auth()->user()->hasRole(['superadmin','staff']))
<script>
(function () {
    var overlay = document.getElementById('moreMenuOverlay');
    var sheet   = document.getElementById('moreMenuSheet');
    var btn     = document.getElementById('btnMoreMenu');
    if (!btn || !sheet) return;

    btn.addEventListener('click', function () {
        overlay.style.display = 'block';
        sheet.style.display   = 'block';
        setTimeout(function () { sheet.style.transform = 'translateY(0)'; }, 10);
    });

    window.closeMoreMenu = function () {
        sheet.style.transform = 'translateY(100%)';
        setTimeout(function () {
            overlay.style.display = 'none';
            sheet.style.display   = 'none';
        }, 260);
    };

    // Close on link click
    sheet.querySelectorAll('a').forEach(function (a) {
        a.addEventListener('click', function () { window.closeMoreMenu(); });
    });
})();
</script>
@endif
</body>
</html>
