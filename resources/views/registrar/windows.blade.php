@extends('layouts.registrar')

@section('content')
<div class="bg-white p-4 rounded shadow-sm">
    <h5 class="mb-4"><i class="bi bi-windows text-primary"></i> Window Queue Management</h5>

    <div class="table-responsive">
        <table class="table table-bordered text-center align-middle">
            <thead class="table-primary">
                <tr>
                    <th>Window Name</th>
                    <th>Status</th>
                    <th>Assigned To</th>
                    <th>Student ID</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($windows as $window)
                    <tr>
                        <td>{{ $window->name }}</td>
                        <td>
                            @if($window->is_occupied)
                                <span class="badge bg-danger">Occupied</span>
                            @else
                                <span class="badge bg-success">Available</span>
                            @endif
                        </td>
                        <td>{{ $window->assignedRequest->full_name ?? '—' }}</td>
                        <td>{{ $window->assignedRequest->student_id ?? '—' }}</td>
                        <td>
                            @if($window->is_occupied)
                                <form action="{{ route('window.release', $window->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button class="btn btn-sm btn-outline-danger">Release</button>
                                </form>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-muted">No windows found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
