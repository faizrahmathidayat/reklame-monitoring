<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>(function(){var t=localStorage.getItem('reklame-theme');if(t==='light')document.documentElement.setAttribute('data-theme','light');})();</script>
    <title>@yield('title', 'Dashboard') — Reklame Monitoring</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg-base:       #0f172a;
            --bg-surface:    #1e293b;
            --bg-elevated:   #263348;
            --border-color:  rgba(99, 102, 241, 0.18);
            --text-primary:  #f1f5f9;
            --text-muted:    #94a3b8;
            --text-dim:      #64748b;
            --accent:        #6366f1;
            --accent-soft:   rgba(99, 102, 241, 0.15);
            --accent-hover:  rgba(99, 102, 241, 0.25);
            --topbar-bg:     rgba(30, 41, 59, 0.88);
            --sidebar-width: 260px;
            --topbar-height: 60px;
            --danger:        #ef4444;
            --success:       #22c55e;
            --warning:       #f59e0b;
            --info:          #3b82f6;
        }

        html[data-theme="light"] {
            --bg-base:      #f8f9ff;
            --bg-surface:   #ffffff;
            --bg-elevated:  #eef0ff;
            --border-color: rgba(99, 102, 241, 0.18);
            --text-primary: #1e293b;
            --text-muted:   #475569;
            --text-dim:     #94a3b8;
            --accent-soft:  rgba(99, 102, 241, 0.1);
            --accent-hover: rgba(99, 102, 241, 0.12);
            --topbar-bg:    rgba(255, 255, 255, 0.92);
        }

        * { font-family: 'Inter', sans-serif; box-sizing: border-box; }

        body {
            background-color: var(--bg-base);
            color: var(--text-primary);
            margin: 0;
            min-height: 100vh;
        }

        /* ── Scrollbar ─────────────────────────────────────────── */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: var(--bg-base); }
        ::-webkit-scrollbar-thumb { background: var(--bg-elevated); border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--accent); }

        /* ── Sidebar ───────────────────────────────────────────── */
        #sidebar {
            position: fixed;
            top: 0; left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--bg-surface);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            z-index: 1040;
            transition: transform 0.3s ease;
            overflow-y: auto;
        }

        .sidebar-brand {
            padding: 1.25rem 1.25rem 1rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            flex-shrink: 0;
        }
        .sidebar-brand-icon {
            width: 40px; height: 40px;
            background: linear-gradient(135deg, var(--accent), #8b5cf6);
            border-radius: 0.6rem;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem; color: white;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.35);
        }
        .sidebar-brand-text { line-height: 1.2; }
        .sidebar-brand-text strong {
            display: block;
            color: var(--text-primary);
            font-size: 0.95rem;
            font-weight: 700;
        }
        .sidebar-brand-text span {
            color: var(--text-dim);
            font-size: 0.72rem;
        }

        .sidebar-nav { padding: 0.75rem 0.75rem; flex: 1; }

        .nav-section-label {
            color: var(--text-dim);
            font-size: 0.65rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: 0.75rem 0.6rem 0.35rem;
        }

        .nav-item { margin-bottom: 2px; }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: var(--text-muted);
            padding: 0.6rem 0.75rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            transition: background 0.15s, color 0.15s;
        }
        .nav-link .nav-icon {
            width: 18px;
            text-align: center;
            font-size: 0.875rem;
            flex-shrink: 0;
        }
        .nav-link:hover {
            background: var(--accent-hover);
            color: var(--text-primary);
        }
        .nav-link.active {
            background: var(--accent-soft);
            color: var(--accent);
            font-weight: 600;
        }
        .nav-link.active .nav-icon { color: var(--accent); }

        /* Dropdown sub-menu */
        .nav-submenu { padding-left: 1rem; }
        .nav-submenu .nav-link {
            font-size: 0.825rem;
            padding: 0.45rem 0.75rem;
            color: var(--text-dim);
        }
        .nav-submenu .nav-link:hover { color: var(--text-primary); }
        .nav-submenu .nav-link.active { color: var(--accent); background: var(--accent-soft); }

        .nav-link[data-bs-toggle="collapse"] .chevron {
            margin-left: auto;
            font-size: 0.7rem;
            transition: transform 0.2s;
        }
        .nav-link[data-bs-toggle="collapse"]:not(.collapsed) .chevron {
            transform: rotate(180deg);
        }

        /* Role badge */
        .role-badge {
            display: inline-block;
            padding: 0.15rem 0.5rem;
            border-radius: 0.3rem;
            font-size: 0.65rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .role-badge.superadmin { background: rgba(99,102,241,0.2); color: #a5b4fc; }
        .role-badge.staff      { background: rgba(34,197,94,0.15); color: #86efac; }
        .role-badge.finance    { background: rgba(245,158,11,0.15); color: #fcd34d; }

        .sidebar-footer {
            padding: 0.875rem 1rem;
            border-top: 1px solid var(--border-color);
            flex-shrink: 0;
        }
        .sidebar-user { display: flex; align-items: center; gap: 0.625rem; }
        .sidebar-user-avatar {
            width: 34px; height: 34px;
            background: linear-gradient(135deg, var(--accent), #8b5cf6);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.8rem; font-weight: 700; color: white;
            flex-shrink: 0;
        }
        .sidebar-user-info { flex: 1; min-width: 0; }
        .sidebar-user-name {
            color: var(--text-primary);
            font-size: 0.825rem;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* ── Sidebar Overlay (mobile) ──────────────────────────── */
        #sidebarOverlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.6);
            z-index: 1039;
        }
        #sidebarOverlay.show { display: block; }

        /* ── Main Wrapper ──────────────────────────────────────── */
        #mainWrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: margin-left 0.3s ease;
        }

        /* ── Topbar ────────────────────────────────────────────── */
        #topbar {
            position: sticky;
            top: 0;
            height: var(--topbar-height);
            background: var(--topbar-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            padding: 0 1.25rem;
            gap: 1rem;
            z-index: 100;
        }

        #menuToggle {
            display: none;
            background: none;
            border: 1px solid var(--border-color);
            color: var(--text-muted);
            width: 36px; height: 36px;
            border-radius: 0.45rem;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.15s;
        }
        #menuToggle:hover { background: var(--accent-hover); color: var(--text-primary); }

        .topbar-title {
            flex: 1;
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .topbar-actions { display: flex; align-items: center; gap: 0.5rem; }

        .topbar-btn {
            background: none;
            border: 1px solid var(--border-color);
            color: var(--text-muted);
            width: 36px; height: 36px;
            border-radius: 0.45rem;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            text-decoration: none;
            font-size: 0.875rem;
            transition: background 0.15s, color 0.15s;
        }
        .topbar-btn:hover { background: var(--accent-hover); color: var(--text-primary); }

        /* ── Page Content ──────────────────────────────────────── */
        .page-content {
            flex: 1;
            padding: 1.5rem;
        }

        .page-header {
            margin-bottom: 1.5rem;
        }
        .page-header h2 {
            font-size: 1.35rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }
        .page-header .breadcrumb {
            background: none;
            padding: 0;
            margin: 0;
            font-size: 0.8rem;
        }
        .page-header .breadcrumb-item { color: var(--text-dim); }
        .page-header .breadcrumb-item.active { color: var(--text-muted); }
        .page-header .breadcrumb-item + .breadcrumb-item::before { color: var(--text-dim); }

        /* ── Cards ─────────────────────────────────────────────── */
        .card-dark {
            background: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: 0.875rem;
        }
        .card-dark .card-header {
            background: transparent;
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 1.25rem;
        }
        .card-dark .card-body { padding: 1.25rem; }
        .card-dark .card-title {
            color: var(--text-primary);
            font-size: 0.95rem;
            font-weight: 600;
            margin: 0;
        }

        /* Stat cards */
        .stat-card {
            background: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: 0.875rem;
            padding: 1.25rem;
            transition: border-color 0.2s, transform 0.2s;
        }
        .stat-card:hover {
            border-color: rgba(99, 102, 241, 0.4);
            transform: translateY(-2px);
        }
        .stat-icon {
            width: 44px; height: 44px;
            border-radius: 0.625rem;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem;
            margin-bottom: 0.875rem;
        }
        .stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text-primary);
            line-height: 1;
            margin-bottom: 0.25rem;
        }
        .stat-label {
            font-size: 0.78rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        /* ── Form Controls (dark) ──────────────────────────────── */
        .form-control, .form-select {
            background: var(--bg-base);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            border-radius: 0.5rem;
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
            font-size: 0.825rem;
            font-weight: 500;
            margin-bottom: 0.4rem;
        }
        .form-select option { background: var(--bg-surface); }
        .input-group-text {
            background: var(--bg-elevated);
            border: 1px solid var(--border-color);
            color: var(--text-dim);
        }

        /* ── Tables ────────────────────────────────────────────── */
        .table-dark-custom { color: var(--text-primary); }
        .table-dark-custom thead th {
            background: var(--bg-elevated);
            color: var(--text-muted);
            border-color: var(--border-color);
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 0.75rem 1rem;
        }
        .table-dark-custom tbody td {
            border-color: var(--border-color);
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            vertical-align: middle;
        }
        .table-dark-custom tbody tr:hover { background: rgba(99,102,241,0.05); }

        /* ── Status Badges ─────────────────────────────────────── */
        .status-badge {
            display: inline-block;
            padding: 0.2rem 0.6rem;
            border-radius: 0.35rem;
            font-size: 0.7rem;
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

        /* ── Buttons ───────────────────────────────────────────── */
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

        /* ── Alert ─────────────────────────────────────────────── */
        .alert { border-radius: 0.625rem; font-size: 0.875rem; }

        /* ── Pagination ────────────────────────────────────────── */
        .pagination { gap: 3px; }
        .pagination .page-link {
            background: var(--bg-surface);
            border-color: var(--border-color);
            color: var(--text-muted);
            border-radius: 0.4rem !important;
            font-size: 0.8rem;
            padding: 0.3rem 0.6rem;
            min-width: 32px;
            text-align: center;
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

        /* ── Modal Dark ─────────────────────────────────────────── */
        .modal-dark {
            background: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: 0.875rem;
        }
        .modal-dark-header {
            border-bottom-color: var(--border-color) !important;
            padding: 1rem 1.25rem;
        }
        .modal-dark-header .modal-title { color: var(--text-primary); font-size: 0.95rem; }
        .modal-dark-footer {
            border-top-color: var(--border-color) !important;
            padding: 0.75rem 1.25rem;
        }
        .modal-backdrop { background-color: #000; }
        .modal-backdrop.show { opacity: 0.65; }

        /* ── Footer ────────────────────────────────────────────── */
        .page-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--border-color);
            color: var(--text-dim);
            font-size: 0.75rem;
            text-align: center;
        }

        /* ── Responsive ────────────────────────────────────────── */
        @media (max-width: 991.98px) {
            #sidebar { transform: translateX(-100%); }
            #sidebar.open { transform: translateX(0); }
            #mainWrapper { margin-left: 0; }
            #menuToggle { display: flex; }

            .page-content { padding: 1rem; }

            .stat-value { font-size: 1.4rem; }
        }

        @media (max-width: 575.98px) {
            .page-content { padding: 0.875rem; }
            .topbar-title { font-size: 0.875rem; }
        }

        /* ── Light Mode Overrides ──────────────────────────────── */
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

        html[data-theme="light"] .btn-close-white       { filter: invert(1) brightness(0); }

        html[data-theme="light"] .alert { color: var(--text-primary) !important; }

        html[data-theme="light"] .table-dark-custom tbody tr:hover { background: rgba(99,102,241,0.06); }

        /* ── Page Loader ── */
        #page-loader {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 9999;
            background: rgba(15,23,42,0.75);
            backdrop-filter: blur(2px);
            -webkit-backdrop-filter: blur(2px);
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 10px;
        }
        #page-loader.show { display: flex; }
        html[data-theme="light"] #page-loader { background: rgba(248,249,255,0.8); }
        .page-loader-ring {
            width: 42px; height: 42px;
            border: 3px solid rgba(99,102,241,0.2);
            border-top-color: #6366f1;
            border-radius: 50%;
            animation: loaderSpin 0.65s linear infinite;
        }
        @keyframes loaderSpin { to { transform: rotate(360deg); } }
        .page-loader-text {
            font-size: 0.75rem;
            color: var(--text-muted);
            letter-spacing: 0.04em;
        }
    </style>

    @stack('styles')
</head>
<body>

<div id="page-loader">
    <div class="page-loader-ring"></div>
    <span class="page-loader-text">Memuat...</span>
</div>

{{-- ── Sidebar Overlay (mobile) ── --}}
<div id="sidebarOverlay"></div>

{{-- ── Sidebar ── --}}
<nav id="sidebar">
    {{-- Brand --}}
    <a href="{{ route('dashboard') }}" class="sidebar-brand">
        <div class="sidebar-brand-icon">
            <i class="fa-solid fa-rectangle-ad"></i>
        </div>
        <div class="sidebar-brand-text">
            <strong>Reklame</strong>
            <span>Monitoring System</span>
        </div>
    </a>

    {{-- Navigation --}}
    <div class="sidebar-nav">
        <p class="nav-section-label">Menu Utama</p>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-gauge nav-icon"></i> Dashboard
                </a>
            </li>
        </ul>

        <p class="nav-section-label mt-2">Data SPK</p>
        <ul class="nav flex-column">
            @if(auth()->user()->hasRole(['superadmin','staff','finance']))
            <li class="nav-item">
                <a href="#menuSpk"
                   class="nav-link {{ request()->routeIs('spk.*') ? '' : 'collapsed' }}"
                   data-bs-toggle="collapse"
                   aria-expanded="{{ request()->routeIs('spk.*') ? 'true' : 'false' }}">
                    <i class="fa-solid fa-file-contract nav-icon"></i>
                    Data SPK
                    <i class="fa-solid fa-chevron-down chevron"></i>
                </a>
                <div class="collapse {{ request()->routeIs('spk.*') ? 'show' : '' }}" id="menuSpk">
                    <ul class="nav flex-column nav-submenu">
                        <li class="nav-item">
                            <a href="{{ route('spk.index') }}" class="nav-link {{ request()->routeIs('spk.index') ? 'active' : '' }}">
                                <i class="fa-solid fa-list nav-icon"></i> Semua SPK
                            </a>
                        </li>
                        @if(auth()->user()->hasRole(['superadmin','staff']))
                        <li class="nav-item">
                            <a href="{{ route('spk.create') }}" class="nav-link {{ request()->routeIs('spk.create') ? 'active' : '' }}">
                                <i class="fa-solid fa-plus nav-icon"></i> Tambah SPK
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            </li>
            @endif

            @if(auth()->user()->hasRole(['superadmin','staff','finance']))
            <li class="nav-item">
                <a href="{{ route('reklame.index') }}" class="nav-link {{ request()->routeIs('reklame.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-layer-group nav-icon"></i> Data Reklame
                </a>
            </li>
            @endif
        </ul>

        <p class="nav-section-label mt-2">Laporan</p>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="{{ route('report') }}" class="nav-link {{ request()->routeIs('report') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-bar nav-icon"></i> Laporan Reklame
                </a>
            </li>
        </ul>

        @if(auth()->user()->isSuperadmin())
        <p class="nav-section-label mt-2">Master Data</p>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="#menuMaster"
                   class="nav-link collapsed"
                   data-bs-toggle="collapse"
                   aria-expanded="false">
                    <i class="fa-solid fa-database nav-icon"></i>
                    Master Data
                    <i class="fa-solid fa-chevron-down chevron"></i>
                </a>
                <div class="collapse {{ request()->is('master/*') ? 'show' : '' }}" id="menuMaster">
                    <ul class="nav flex-column nav-submenu">
                        <li class="nav-item">
                            <a href="{{ route('master.wilayah.index') }}" class="nav-link {{ request()->routeIs('master.wilayah.*') ? 'active' : '' }}">
                                <i class="fa-solid fa-map-marker-alt nav-icon"></i> Wilayah / DC
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('master.cabang.index') }}" class="nav-link {{ request()->routeIs('master.cabang.*') ? 'active' : '' }}">
                                <i class="fa-solid fa-code-branch nav-icon"></i> Cabang
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('master.toko.index') }}" class="nav-link {{ request()->routeIs('master.toko.*') ? 'active' : '' }}">
                                <i class="fa-solid fa-store nav-icon"></i> Toko
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('master.brand.index') }}" class="nav-link {{ request()->routeIs('master.brand.*') ? 'active' : '' }}">
                                <i class="fa-solid fa-tag nav-icon"></i> Brand
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('master.pic.index') }}" class="nav-link {{ request()->routeIs('master.pic.*') ? 'active' : '' }}">
                                <i class="fa-solid fa-user-tie nav-icon"></i> PIC
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="nav-item">
                <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-users-gear nav-icon"></i> Manajemen User
                </a>
            </li>
        </ul>
        @endif
    </div>

    {{-- User Footer --}}
    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="sidebar-user-avatar">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="sidebar-user-info">
                <div class="sidebar-user-name">{{ auth()->user()->name }}</div>
                <span class="role-badge {{ auth()->user()->role }}">{{ auth()->user()->role }}</span>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="ms-1">
                @csrf
                <button type="submit" class="topbar-btn" title="Logout">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </button>
            </form>
        </div>
    </div>
</nav>

{{-- ── Main Wrapper ── --}}
<div id="mainWrapper">

    {{-- Topbar --}}
    <header id="topbar">
        <button id="menuToggle" aria-label="Toggle menu">
            <i class="fa-solid fa-bars"></i>
        </button>
        <div class="topbar-title">@yield('page-title', 'Dashboard')</div>
        <div class="topbar-actions">
            <span class="d-none d-sm-inline" style="color:var(--text-dim); font-size:0.75rem;">
                {{ now()->isoFormat('dddd, D MMMM Y') }}
            </span>
            <button id="themeToggle" class="topbar-btn" title="Mode Terang">
                <i id="themeIcon" class="fa-solid fa-sun"></i>
            </button>
        </div>
    </header>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="mx-3 mt-3">
        <div class="alert alert-success alert-dismissible d-flex align-items-center gap-2" role="alert"
             style="background:rgba(34,197,94,0.1);border:1px solid rgba(34,197,94,0.3);color:#86efac;">
            <i class="fa-solid fa-circle-check"></i>
            <span>{{ session('success') }}</span>
            <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="alert"></button>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="mx-3 mt-3">
        <div class="alert alert-danger alert-dismissible d-flex align-items-center gap-2" role="alert"
             style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);color:#fca5a5;">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span>{{ session('error') }}</span>
            <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="alert"></button>
        </div>
    </div>
    @endif

    {{-- Page Content --}}
    <main class="page-content">
        @yield('content')
    </main>

    <footer class="page-footer">
        &copy; {{ date('Y') }} Reklame Monitoring System. All rights reserved.
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // ── Sidebar toggle (mobile) ──
    const sidebar  = document.getElementById('sidebar');
    const overlay  = document.getElementById('sidebarOverlay');
    const menuBtn  = document.getElementById('menuToggle');

    menuBtn.addEventListener('click', () => {
        sidebar.classList.toggle('open');
        overlay.classList.toggle('show');
    });
    overlay.addEventListener('click', () => {
        sidebar.classList.remove('open');
        overlay.classList.remove('show');
    });

    // ── Theme Toggle ──
    var themeToggle = document.getElementById('themeToggle');
    var themeIcon   = document.getElementById('themeIcon');

    function applyTheme(t) {
        if (t === 'light') {
            document.documentElement.setAttribute('data-theme', 'light');
            themeIcon.className = 'fa-solid fa-moon';
            themeToggle.title   = 'Mode Gelap';
        } else {
            document.documentElement.removeAttribute('data-theme');
            themeIcon.className = 'fa-solid fa-sun';
            themeToggle.title   = 'Mode Terang';
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

<script>
(function () {
    var loader = document.getElementById('page-loader');
    function showLoader() { loader.classList.add('show'); }

    // Show on link navigation (skip: modal/collapse/dropdown toggles, hash, new tab)
    document.addEventListener('click', function (e) {
        var el = e.target.closest('a[href]');
        if (!el) return;
        var href = el.getAttribute('href');
        if (!href || href === '#' || href.charAt(0) === '#') return;
        if (href.startsWith('javascript:')) return;
        if (el.getAttribute('target') === '_blank') return;
        if (el.hasAttribute('data-bs-toggle') || el.hasAttribute('data-bs-dismiss')) return;
        if (el.hasAttribute('data-no-loading')) return;
        showLoader();
    });

    // Show on form submit (skip if confirm dialog was cancelled)
    document.addEventListener('submit', function (e) {
        if (e.defaultPrevented) return;
        showLoader();
    });

    // Hide if page restored from bfcache (browser back button)
    window.addEventListener('pageshow', function (e) {
        if (e.persisted) loader.classList.remove('show');
    });
})();
</script>
</body>
</html>
