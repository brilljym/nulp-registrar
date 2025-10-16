<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Dashboard - NU Lipa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .sidebar { width: 250px; background-color: #1a237e; color: white; min-height: 100vh; }
        .sidebar a { color: white; display: block; padding: 10px 20px; text-decoration: none; }
        .sidebar a:hover { background-color: #3949ab; }
        .sidebar .active { background-color: #ffca28; color: black; font-weight: bold; }
    </style>
</head>
<body>
<div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar p-3">
        <div class="text-center mb-4">
            <img src="{{ asset('logo.png') }}" alt="NU Logo" width="80">
            <h5 class="mt-2">NU Lipa</h5>
        </div>
        <a href="{{ route('registrar.all') }}" class="{{ request()->is('registrar/dashboard') ? 'active' : '' }}">ALL</a>
        <a href="{{ route('registrar.completed') }}" class="{{ request()->is('registrar/completed') ? 'active' : '' }}">COMPLETED▶</a>
        <a href="{{ route('registrar.pending') }}" class="{{ request()->is('registrar/pending') ? 'active' : '' }}">PENDING</a>
        <a href="#">HISTORY</a>
    </div>

    <!-- Main -->
    <div class="flex-grow-1">
        <nav class="navbar navbar-light bg-light shadow-sm">
            <div class="container-fluid justify-content-between">
                <div>
                    <h4 class="mb-0 fw-bold text-primary">REGISTRAR DASHBOARD</h4>
                    <small class="text-muted">Welcome to the NU Lipa Document Request System</small>
                </div>
                <div class="d-flex align-items-center">
                    <form method="GET" action="{{ route('registrar.dashboard') }}" class="d-flex align-items-center me-2">
                        <input type="text" name="search" class="form-control me-2"
                            placeholder="Search by name, ID, or document"
                            value="{{ request('search') }}">
                        <button class="btn btn-outline-primary" type="submit">Search</button>
                    </form>
                    <a href="{{ route('logout') }}" class="btn btn-outline-primary">Logout</a>
                </div>
            </div>
        </nav>

        <div class="p-4">
            @yield('content')
        </div>
    </div>
</div>
</body>
</html>
