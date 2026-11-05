<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Student Portal</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/student-portal.css') }}" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body { min-height: 100vh; }
        .navbar { min-height: 64px; }
        .sidebar { position: sticky; top: 0; align-self: flex-start; }
        .portal-main { min-width: 0; }
        .sidebar .nav-link, .sidebar button.nav-link { text-decoration: none; cursor: pointer; }
        .portal-brand { color: #fff; text-decoration: none; }
        .portal-brand:hover { color: #F3E3D7; }
        .profile-card { border: 1px solid rgba(94,48,35,.12); border-radius: 12px; }
        .chart-card { height: 100%; }
        .navbar .btn { white-space: nowrap; }
        @media (max-width: 767.98px) {
            .sidebar { position: static; min-height: auto; }
            .portal-main { padding: 1rem !important; }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark shadow-sm">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold portal-brand" href="{{ route('dashboard') }}">Student Portal</a>
            <div class="d-flex align-items-center gap-3">
                <span class="text-white">Welcome, <strong>{{ auth()->user()->name ?? 'Admin' }}</strong></span>
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-secondary-custom px-3">Log Out</button>
                </form>
            </div>
        </div>
    </nav>

    <main class="container-fluid px-0 pb-4">
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
