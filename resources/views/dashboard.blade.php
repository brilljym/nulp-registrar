<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - NU Registrar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-white">
<div class="container mt-5">
    <div class="text-end">
        <a href="{{ route('logout') }}" class="btn btn-danger">Logout</a>
    </div>

    <div class="mt-5 text-center">
        <h1>Welcome, {{ Auth::user()->first_name }}!</h1>
        <p class="lead">You are logged in as a <strong>{{ Auth::user()->role }}</strong>.</p>
    </div>
</div>
</body>
</html>
