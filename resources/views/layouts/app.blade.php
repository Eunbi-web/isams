<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'ISAMS') }} — @yield('title', 'Dashboard')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
:root{
            --primary:       #1a3a5c;
            --primary-light: #2a5298;
            --accent:        #e8b84b;
            --accent-light:  #f5d07a;
            --danger:        #c0392b;
            --success:       #1a7a4a;
            --warning:       #d68910;
            --info:          #1a6b8a;
            --bg:            #f0f4f8;
            --bg-card:       #ffffff;
            --sidebar-bg:    #0d2137;
            --sidebar-text:  #a8c0d6;
            --sidebar-hover: #1a3a5c;
            --text:          #1c2b3a;
            --text-muted:    #5a7a94;
            --border:        #d0dce8;
            --shadow:        0 2px 12px rgba(26,58,92,0.10);
            --radius:        12px;
            --radius-sm:     8px;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            font-size: 15px;
        }

        /* Light mode overrides */
        body[data-theme="light"] {
            --bg:            #f0f4f8;
            --bg-card:       #ffffff;
            --sidebar-bg:    #ffffff;
            --sidebar-text:  #1c2b3a;
            --sidebar-hover: #e8f0fb;
            --text:          #1c2b3a;
            --text-muted:    #5a7a94;
            --border:        #d0dce8;
            --shadow:        0 2px 12px rgba(26,58,92,0.10);
        }

        /* Dark mode overrides */
        body[data-theme="dark"] {
            --bg:            #0b1220;
            --bg-card:       #0f1a2d;
            --sidebar-bg:    #081022;
            --sidebar-text:  #9fb3c8;
            --sidebar-hover: #1a3a5c;
            --text:          #dbe8f5;
            --text-muted:    #6f8aa7;
            --border:        rgba(160,190,220,0.22);
            --shadow:        0 8px 30px rgba(0,0,0,0.35);
        }


        /* ── SIDEBAR ── */
        .sidebar {
            width: 260px;
            min-width: 260px;
            background: var(--sidebar-bg);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 100;
            overflow-y: auto;
            transition: transform .3s ease;
        }

        .sidebar-brand {
            padding: 28px 20px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.07);
        }
        .sidebar-brand .brand-logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .brand-icon {
            width: 44px; height: 44px;
            background: linear-gradient(135deg, var(--accent), #c8972b);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px; color: #fff;
            box-shadow: 0 4px 14px rgba(232,184,75,0.4);
        }
        .brand-text .name {
            font-family: 'Sora', sans-serif;
            font-size: 16px; font-weight: 700;
            color: #fff; line-height: 1.1;
        }
        .brand-text .sub {
            font-size: 10px; color: var(--sidebar-text);
            text-transform: uppercase; letter-spacing: 1px;
        }

        .sidebar-section {
            padding: 18px 12px 6px;
        }
        .sidebar-section-label {
            font-size: 10px; font-weight: 600;
            text-transform: uppercase; letter-spacing: 1.5px;
            color: rgba(168,192,214,0.45);
            padding: 0 8px 8px;
        }
        .nav-item {
            display: flex; align-items: center; gap: 12px;
            padding: 10px 12px;
            border-radius: var(--radius-sm);
            color: var(--sidebar-text);
            text-decoration: none;
            font-size: 14px; font-weight: 500;
            transition: all .2s ease;
            margin-bottom: 2px;
        }
        .nav-item i { width: 18px; text-align: center; font-size: 15px; }
        .nav-item:hover, .nav-item.active {
            background: var(--sidebar-hover);
            color: #fff;
        }
        .nav-item.active { color: var(--accent); }
        .nav-item .badge {
            margin-left: auto;
            background: var(--accent);
            color: var(--primary);
            font-size: 10px; font-weight: 700;
            padding: 2px 7px; border-radius: 20px;
        }

        .sidebar-footer {
            margin-top: auto;
            padding: 16px 12px;
            border-top: 1px solid rgba(255,255,255,0.07);
        }
        .user-card {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 12px;
            background: rgba(255,255,255,0.05);
            border-radius: var(--radius-sm);
        }
        .user-avatar {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, var(--accent), var(--primary-light));
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 14px; color: #fff;
        }
        .user-info .uname { font-size: 13px; font-weight: 600; color: #fff; }
        .user-info .urole { font-size: 11px; color: var(--sidebar-text); }
        .user-logout {
            margin-left: auto; color: var(--sidebar-text);
            text-decoration: none; font-size: 15px;
            transition: color .2s;
        }
        .user-logout:hover { color: var(--danger); }

        /* ── MAIN CONTENT ── */
        .main {
            margin-left: 260px;
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* ── TOPBAR ── */
        .topbar {
            background: var(--bg-card);
            border-bottom: 1px solid var(--border);
            padding: 0 28px;
            height: 64px;
            display: flex;
            align-items: center;
            gap: 16px;
            position: sticky; top: 0; z-index: 90;
            box-shadow: 0 1px 8px rgba(0,0,0,0.05);
        }
        .topbar-title {
            font-family: 'Sora', sans-serif;
            font-size: 18px; font-weight: 700;
            color: var(--primary);
        }
        .topbar-subtitle { font-size: 13px; color: var(--text-muted); margin-top: 1px; }
        .topbar-right { margin-left: auto; display: flex; align-items: center; gap: 14px; }
        .topbar-btn {
            width: 38px; height: 38px;
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            display: flex; align-items: center; justify-content: center;
            color: var(--text-muted); cursor: pointer;
            text-decoration: none;
            transition: all .2s;
        }
        .topbar-btn:hover { background: var(--primary); color: #fff; border-color: var(--primary); }

        .school-badge {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: #fff;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        /* ── PAGE CONTENT ── */
        .page-content {
            padding: 28px;
            flex: 1;
        }

        /* ── CARDS ── */
        .card {
            background: var(--bg-card);
            border-radius: var(--radius);
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
            overflow: hidden;
        }
        .card-header {
            padding: 18px 22px;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; gap: 12px;
        }
        .card-header h2 {
            font-family: 'Sora', sans-serif;
            font-size: 15px; font-weight: 700;
            color: var(--primary);
        }
        .card-header .card-actions { margin-left: auto; display: flex; gap: 8px; }
        .card-body { padding: 22px; }

        /* ── STATS ── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 18px;
            margin-bottom: 24px;
        }
        .stat-card {
            background: var(--bg-card);
            border-radius: var(--radius);
            border: 1px solid var(--border);
            padding: 20px 22px;
            box-shadow: var(--shadow);
            display: flex; align-items: center; gap: 16px;
            transition: transform .2s, box-shadow .2s;
        }
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(26,58,92,0.13); }
        .stat-icon {
            width: 52px; height: 52px;
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 22px; flex-shrink: 0;
        }
        .stat-icon.blue   { background: #e8f0fb; color: var(--primary-light); }
        .stat-icon.gold   { background: #fef6e0; color: #c8972b; }
        .stat-icon.green  { background: #e2f5ec; color: var(--success); }
        .stat-icon.red    { background: #fde8e6; color: var(--danger); }
        .stat-icon.teal   { background: #e0f3f8; color: var(--info); }
        .stat-icon.purple { background: #efe8fb; color: #7b4fcf; }
        .stat-info .label { font-size: 12px; color: var(--text-muted); font-weight: 500; }
        .stat-info .value {
            font-family: 'Sora', sans-serif;
            font-size: 28px; font-weight: 800;
            color: var(--primary); line-height: 1.1;
        }
        .stat-info .change { font-size: 12px; color: var(--success); margin-top: 2px; }
        .stat-info .change.down { color: var(--danger); }

        /* ── TABLES ── */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        thead th {
            background: #f7fafc;
            padding: 11px 16px;
            font-size: 11px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.8px;
            color: var(--text-muted);
            text-align: left;
            border-bottom: 2px solid var(--border);
        }
        tbody tr { transition: background .15s; }
        tbody tr:hover { background: #f7fafc; }
        tbody td {
            padding: 13px 16px;
            font-size: 14px;
            border-bottom: 1px solid var(--border);
            color: var(--text);
        }
        tbody tr:last-child td { border-bottom: none; }

        /* ── BADGES ── */
        .badge {
            display: inline-flex; align-items: center;
            padding: 3px 10px; border-radius: 20px;
            font-size: 11px; font-weight: 600;
        }
        .badge-success { background: #e2f5ec; color: #1a7a4a; }
        .badge-danger  { background: #fde8e6; color: #c0392b; }
        .badge-warning { background: #fef3cd; color: #a07c00; }
        .badge-info    { background: #e0f3f8; color: #1a6b8a; }
        .badge-primary { background: #e8f0fb; color: #2a5298; }
        .badge-gray    { background: #eef1f5; color: #5a7a94; }

        /* ── BUTTONS ── */
        .btn {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 9px 18px;
            border-radius: var(--radius-sm);
            font-size: 13px; font-weight: 600;
            cursor: pointer; border: none;
            text-decoration: none;
            transition: all .2s;
        }
        .btn-primary { background: var(--primary); color: #fff; }
        .btn-primary:hover { background: var(--primary-light); }
        .btn-accent  { background: var(--accent); color: var(--primary); }
        .btn-accent:hover { background: var(--accent-light); }
        .btn-danger  { background: var(--danger); color: #fff; }
        .btn-success { background: var(--success); color: #fff; }
        .btn-outline {
            background: transparent;
            border: 1.5px solid var(--border);
            color: var(--text-muted);
        }
        .btn-outline:hover { border-color: var(--primary); color: var(--primary); }
        .btn-sm { padding: 6px 13px; font-size: 12px; }
        .btn-icon { padding: 7px 10px; }

        /* ── FORMS ── */
        .form-group { margin-bottom: 18px; }
        .form-label {
            display: block;
            font-size: 13px; font-weight: 600;
            color: var(--text); margin-bottom: 6px;
        }
        .form-control {
            width: 100%; padding: 10px 14px;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            font-size: 14px; color: var(--text);
            background: #fff;
            transition: border-color .2s;
            font-family: inherit;
        }
        .form-control:focus {
            outline: none;
            border-color: var(--primary-light);
            box-shadow: 0 0 0 3px rgba(42,82,152,0.1);
        }
        select.form-control { cursor: pointer; }

        /* ── ALERTS ── */
        .alert {
            padding: 13px 18px; border-radius: var(--radius-sm);
            margin-bottom: 18px; font-size: 14px;
            display: flex; align-items: center; gap: 10px;
        }
        .alert-success { background: #e2f5ec; border-left: 4px solid var(--success); color: #1a4a2e; }
        .alert-danger  { background: #fde8e6; border-left: 4px solid var(--danger); color: #7a1a14; }
        .alert-warning { background: #fef3cd; border-left: 4px solid var(--warning); color: #6b4a00; }
        .alert-info    { background: #e0f3f8; border-left: 4px solid var(--info); color: #0d3d52; }

        /* ── GRID ── */
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; }

        /* ── UTILITIES ── */
        .text-muted    { color: var(--text-muted); }
        .text-primary  { color: var(--primary); }
        .text-accent   { color: var(--accent); }
        .text-success  { color: var(--success); }
        .text-danger   { color: var(--danger); }
        .fw-bold       { font-weight: 700; }
        .fw-semi       { font-weight: 600; }
        .mt-1 { margin-top: 8px; }
        .mt-2 { margin-top: 16px; }
        .mt-3 { margin-top: 24px; }
        .mb-1 { margin-bottom: 8px; }
        .mb-2 { margin-bottom: 16px; }
        .mb-3 { margin-bottom: 24px; }
        .d-flex { display: flex; }
        .align-center { align-items: center; }
        .gap-2 { gap: 10px; }
        .justify-between { justify-content: space-between; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }

        /* ── AVATAR ── */
        .avatar {
            border-radius: 50%;
            display: inline-flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 13px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: #fff; flex-shrink: 0;
        }
        .avatar-sm { width: 32px; height: 32px; font-size: 12px; }
        .avatar-md { width: 40px; height: 40px; font-size: 15px; }
        .avatar-lg { width: 56px; height: 56px; font-size: 20px; }

        /* ── SEARCH ── */
        .search-box {
            position: relative;
        }
        .search-box i {
            position: absolute; left: 12px; top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted); font-size: 14px;
        }
        .search-box input {
            padding-left: 38px;
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 900px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main { margin-left: 0; }
            .grid-2, .grid-3 { grid-template-columns: 1fr; }
        }

        /* ── ANIMATIONS ── */
        @keyframes fadeInUp {
            from { opacity:0; transform: translateY(16px); }
            to   { opacity:1; transform: translateY(0); }
        }
        .animate { animation: fadeInUp .4s ease both; }
        .delay-1 { animation-delay: .05s; }
        .delay-2 { animation-delay: .10s; }
        .delay-3 { animation-delay: .15s; }
        .delay-4 { animation-delay: .20s; }
        .delay-5 { animation-delay: .25s; }
        .delay-6 { animation-delay: .30s; }

        /* scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #c0d0de; border-radius: 10px; }
    </style>
    @stack('styles')
</head>
<body data-theme="light">


<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="brand-logo">
            <div class="brand-icon"><i class="fas fa-graduation-cap"></i></div>
            <div class="brand-text">
                <div class="name">ISAMS</div>
                <div class="sub">Student Affairs</div>
            </div>
        </div>
    </div>

    <div class="sidebar-section">
        <div class="sidebar-section-label">Main</div>
        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fas fa-th-large"></i> Dashboard
        </a>
    </div>

    <div class="sidebar-section">
        <div class="sidebar-section-label">Records</div>
        <a href="{{ route('students.index') }}" class="nav-item {{ request()->routeIs('students.*') ? 'active' : '' }}">
            <i class="fas fa-user-graduate"></i> Students
        </a>
        <a href="{{ route('organizations.index') }}" class="nav-item {{ request()->routeIs('organizations.*') ? 'active' : '' }}">
            <i class="fas fa-users"></i> Organizations
        </a>
        <a href="{{ route('scholarships.index') }}" class="nav-item {{ request()->routeIs('scholarships.*') ? 'active' : '' }}">
            <i class="fas fa-award"></i> Scholarships
        </a>
    </div>

    <div class="sidebar-section">
        <div class="sidebar-section-label">Services</div>
        <a href="{{ route('counseling.index') }}" class="nav-item {{ request()->routeIs('counseling.*') ? 'active' : '' }}">
            <i class="fas fa-heart"></i> Counseling
        </a>
        <a href="{{ route('discipline.index') }}" class="nav-item {{ request()->routeIs('discipline.*') ? 'active' : '' }}">
            <i class="fas fa-gavel"></i> Discipline
        </a>
        <a href="{{ route('events.index') }}" class="nav-item {{ request()->routeIs('events.*') ? 'active' : '' }}">
            <i class="fas fa-calendar-alt"></i> Events
        </a>
    </div>

    <div class="sidebar-section">
        <div class="sidebar-section-label">System</div>
        <a href="{{ route('reports.index') }}" class="nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
            <i class="fas fa-chart-bar"></i> Reports
        </a>
        <a href="{{ route('settings.index') }}" class="nav-item {{ request()->routeIs('settings.*') ? 'active' : '' }}">
            <i class="fas fa-cog"></i> Settings
        </a>
    </div>

    <div class="sidebar-footer">
        <div class="user-card">
            <div class="user-avatar">
                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
            </div>
            <div class="user-info">
                <div class="uname">{{ auth()->user()->name ?? 'Admin' }}</div>
                <div class="urole">{{ auth()->user()->role ?? 'Administrator' }}</div>
            </div>
            <a href="{{ route('logout') }}" class="user-logout"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
            @csrf
        </form>
    </div>
</aside>

<!-- MAIN CONTENT -->
<div class="main">
    <!-- TOPBAR -->
    <header class="topbar">
        <button class="topbar-btn" id="sidebarToggle" style="display:none;">
            <i class="fas fa-bars"></i>
        </button>
        <div>
            <div class="topbar-title">@yield('page-title', 'Dashboard')</div>
            <div class="topbar-subtitle text-muted" style="font-size:12px;">@yield('page-subtitle', 'Integrated Student Affairs Management System')</div>
        </div>
        <div class="topbar-right">
            <span class="school-badge"><i class="fas fa-university" style="margin-right:6px;"></i>@yield('school', 'University')</span>
            <a href="{{ route('notifications.index') }}" class="topbar-btn" style="position:relative;">
                <i class="fas fa-bell"></i>
                <span style="position:absolute;top:6px;right:6px;width:8px;height:8px;background:var(--accent);border-radius:50%;border:2px solid #fff;"></span>
            </a>

            {{-- Theme toggle (System → Light → Dark) --}}
            @include('components.theme-toggle')

            <a href="{{ route('settings.index') }}" class="topbar-btn"><i class="fas fa-cog"></i></a>
        </div>

    </header>

    <!-- PAGE CONTENT -->
    <div class="page-content">
        @if(session('success'))
            <div class="alert alert-success animate"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger animate"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
        @endif
        @yield('content')
    </div>
</div>

<script>
    (function(){
        // Apply saved theme (system/light/dark) and resolve "system" using OS preference.
        var body = document.body;
        if(!body) return;

        var savedTheme = @json(auth()->user()->theme ?? 'system');
        var resolved = savedTheme;
        if(savedTheme === 'system'){
            var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
            resolved = prefersDark ? 'dark' : 'light';
        }

        body.setAttribute('data-theme', resolved);

        // Theme toggle button state
        var btn = document.getElementById('themeToggleBtn');
        var icon = document.getElementById('themeToggleIcon');
        if(btn){
            var cycle = ['system','light','dark'];
            var idx = Math.max(0, cycle.indexOf(savedTheme));

            function setIcon(){
                if(!icon) return;
                var map = {system:'fas fa-laptop', light:'fas fa-sun', dark:'fas fa-moon'};
                icon.className = map[savedTheme] || 'fas fa-adjust';
                btn.title = 'Theme: ' + savedTheme.charAt(0).toUpperCase()+savedTheme.slice(1);
            }

            setIcon();

            btn.addEventListener('click', function(){
                // cycle: system -> light -> dark -> system
                var next = cycle[(idx + 1) % cycle.length];
                idx = (idx + 1) % cycle.length;
                savedTheme = next;

                // update UI immediately (resolved)
                var nextResolved = next;
                if(next === 'system'){
                    var pDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                    nextResolved = pDark ? 'dark' : 'light';
                }
                body.setAttribute('data-theme', nextResolved);
                setIcon();

                // persist
                var csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                fetch('{{ route('settings.theme.update') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ theme: next })
                }).catch(function(){});
            });
        }
    })();

    const toggle = document.getElementById('sidebarToggle');

    const sidebar = document.getElementById('sidebar');
    if (toggle) {
        toggle.addEventListener('click', () => sidebar.classList.toggle('open'));
    }
    if (window.innerWidth <= 900) {
        toggle.style.display = 'flex';
    }
    window.addEventListener('resize', () => {
        if (window.innerWidth <= 900) toggle.style.display = 'flex';
        else { toggle.style.display = 'none'; sidebar.classList.remove('open'); }
    });
</script>
@stack('scripts')
</body>
</html>
