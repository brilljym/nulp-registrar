<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Management - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Admin Panel</a>
            <a href="{{ route('logout') }}" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </nav>

    <div class="container mt-5">
        <h2 class="mb-4">User Management</h2>

        <table class="table table-bordered table-hover shadow">
            <thead class="table-primary">
                <tr>
                    <th>#</th>
                    <th>Full Name</th>
                    <th>School Email</th>
                    <th>Personal Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $index => $user)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $user->last_name }}, {{ $user->first_name }} {{ $user->middle_name }}</td>
                        <td>{{ $user->school_email }}</td>
                        <td>{{ $user->personal_email }}</td>
                        <td><span class="badge bg-secondary text-uppercase">{{ $user->role->name }}</span></td>
                        <td>
                            <a href="#" class="btn btn-sm btn-warning disabled">Edit</a>
                            <a href="#" class="btn btn-sm btn-danger disabled">Delete</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4">
            <a href="{{ url('/dashboard') }}" class="btn btn-outline-secondary">← Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
