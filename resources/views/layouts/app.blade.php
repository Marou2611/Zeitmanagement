<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Zeitmanagement') }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --sidebar-width: 260px;
            --primary: #3b5998;
            --primary-dark: #2d4373;
            --accent: #e8f0fe;
            --sidebar-bg: #1e2a3a;
            --sidebar-hover: #2d3e52;
            --sidebar-active: #3b5998;
            --sidebar-text: #c8d6e5;
            --topbar-height: 60px;
        }
        * { box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f4f6fb; margin: 0; padding: 0; }

        .sidebar {
            position: fixed; top: 0; left: 0;
            width: var(--sidebar-width); height: 100vh;
            background: var(--sidebar-bg);
            display: flex; flex-direction: column;
            z-index: 1000; transition: transform 0.3s ease; overflow-y: auto;
        }
        .sidebar-brand {
            padding: 1.2rem 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }
        .sidebar-brand-text { color: #fff; font-weight: 700; font-size: 0.95rem; line-height: 1.2; }
        .sidebar-brand-sub { color: var(--sidebar-text); font-size: 0.72rem; }
        .sidebar-section {
            padding: 1.2rem 1rem 0.4rem;
            font-size: 0.68rem; font-weight: 600;
            text-transform: uppercase; letter-spacing: 0.08em; color: #6b7f94;
        }
        .sidebar nav a {
            display: flex; align-items: center; gap: 12px;
            padding: 0.65rem 1.25rem; color: var(--sidebar-text);
            text-decoration: none; font-size: 0.875rem;
            border-radius: 8px; margin: 2px 8px; transition: all 0.18s ease;
        }
        .sidebar nav a:hover { background: var(--sidebar-hover); color: #fff; }
        .sidebar nav a.active {
            background: var(--sidebar-active); color: #fff; font-weight: 600;
            box-shadow: 0 2px 8px rgba(59,89,152,0.35);
        }
        .sidebar nav a i { font-size: 1.05rem; width: 20px; text-align: center; flex-shrink: 0; }

        .sidebar-footer { margin-top: auto; padding: 1rem; border-top: 1px solid rgba(255,255,255,0.08); }
        .user-info { display: flex; align-items: center; gap: 10px; margin-bottom: 0.75rem; }
        .avatar {
            width: 36px; height: 36px; border-radius: 50%;
            background: var(--sidebar-active);
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-weight: 700; font-size: 0.875rem; flex-shrink: 0;
        }
        .user-name { color: #fff; font-size: 0.875rem; font-weight: 500; }
        .user-email { color: var(--sidebar-text); font-size: 0.72rem; }
        .logout-btn {
            display: flex; align-items: center; gap: 8px;
            width: 100%; padding: 0.5rem 0.75rem;
            background: rgba(255,255,255,0.06); border: none; border-radius: 8px;
            color: var(--sidebar-text); font-size: 0.82rem; cursor: pointer; transition: all 0.18s;
        }
        .logout-btn:hover { background: rgba(220,53,69,0.2); color: #ff6b7a; }

        .main-wrapper { margin-left: var(--sidebar-width); min-height: 100vh; display: flex; flex-direction: column; }
        .topbar {
            height: var(--topbar-height); background: #fff;
            border-bottom: 1px solid #e5e9f0;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 1.5rem; position: sticky; top: 0; z-index: 100;
            box-shadow: 0 1px 4px rgba(0,0,0,0.05);
        }
        .topbar-title { font-size: 1.05rem; font-weight: 600; color: #1e2a3a; }
        .topbar-badge {
            background: var(--accent); color: var(--primary);
            font-size: 0.72rem; font-weight: 600; padding: 3px 10px; border-radius: 20px;
        }
        .page-content { padding: 1.75rem; flex: 1; }

        .card { border: none; border-radius: 12px; box-shadow: 0 1px 6px rgba(0,0,0,0.07); }
        .card-header {
            background: #fff; border-bottom: 1px solid #eef0f4;
            border-radius: 12px 12px 0 0 !important;
            padding: 1rem 1.25rem; font-weight: 600; color: #1e2a3a;
        }
        .btn-primary { background: var(--primary) !important; border-color: var(--primary) !important; }
        .btn-primary:hover { background: var(--primary-dark) !important; border-color: var(--primary-dark) !important; }
        .table th {
            font-size: 0.78rem; font-weight: 600; text-transform: uppercase;
            letter-spacing: 0.05em; color: #6b7f94;
            background: #f8f9fc; border-bottom: 2px solid #eef0f4;
        }
        .table td { vertical-align: middle; font-size: 0.875rem; }
        .alert { border-radius: 10px; border: none; font-size: 0.875rem; }

        .sidebar-toggle { display: none; background: none; border: none; font-size: 1.4rem; color: #1e2a3a; cursor: pointer; padding: 4px 8px; }
        @media (max-width: 991px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .main-wrapper { margin-left: 0; }
            .sidebar-toggle { display: block; }
            .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 999; }
            .sidebar-overlay.show { display: block; }
        }
    </style>
</head>
<body>

<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="sidebar-brand-text">Zeitmanagement</div>
        <div class="sidebar-brand-sub">Hochschule Osnabrück</div>
    </div>
    <nav>
        <div class="sidebar-section">Hauptmenü</div>
        <a href="{{ route('semesters.index') }}" class="{{ request()->routeIs('semesters.*') ? 'active' : '' }}">
            <i class="bi bi-calendar3"></i> Semester
        </a>
        <a href="{{ route('lecturers.index') }}" class="{{ request()->routeIs('lecturers.*') ? 'active' : '' }}">
            <i class="bi bi-person-workspace"></i> Dozenten
        </a>
        <a href="{{ route('timetables.index') }}" class="{{ request()->routeIs('timetables.*') ? 'active' : '' }}">
            <i class="bi bi-grid-3x3-gap"></i> Stundenpläne
        </a>
        <div class="sidebar-section">Benachrichtigungen</div>
        <a href="{{ route('notifications.overview') }}" class="{{ request()->routeIs('notifications.*') ? 'active' : '' }}">
            <i class="bi bi-bell"></i> Übersicht
        </a>
        <a href="{{ route('notifications.pending') }}" class="{{ request()->routeIs('notifications.pending') ? 'active' : '' }}">
            <i class="bi bi-hourglass-split"></i> Ausstehend
        </a>
    </nav>
    <div class="sidebar-footer">
        <div class="user-info">
            <div class="avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
            <div>
                <div class="user-name">{{ Auth::user()->name }}</div>
                <div class="user-email">{{ Auth::user()->email }}</div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-btn">
                <i class="bi bi-box-arrow-left"></i> Abmelden
            </button>
        </form>
    </div>
</aside>

<div class="main-wrapper">
    <div class="topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="sidebar-toggle" onclick="toggleSidebar()"><i class="bi bi-list"></i></button>
            @isset($header)
                <span class="topbar-title">{{ $header }}</span>
            @else
                <span class="topbar-title">{{ config('app.name') }}</span>
            @endisset
        </div>
        <span class="topbar-badge">
            <i class="bi bi-circle-fill me-1" style="font-size:0.5rem;color:#22c55e;"></i> Live
        </span>
    </div>

    @if(session('success'))
        <div class="mx-3 mt-3">
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="mx-3 mt-3">
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    <main class="page-content">
        {{ $slot }}
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('show');
        document.getElementById('sidebarOverlay').classList.toggle('show');
    }
</script>
</body>
</html>
