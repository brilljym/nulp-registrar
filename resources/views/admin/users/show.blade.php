@extends('layouts.admin')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">User Details</h4>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back to Users
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Basic Information</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold">Full Name:</td>
                                    <td>{{ $user->last_name }}, {{ $user->first_name }} {{ $user->middle_name }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">School Email:</td>
                                    <td>{{ $user->school_email }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Personal Email:</td>
                                    <td>{{ $user->personal_email }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Role:</td>
                                    <td>
                                        <span class="badge bg-primary">{{ strtoupper($user->role->name ?? 'N/A') }}</span>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        @if($user->role_id == 3 && $user->student)
                        <div class="col-md-6">
                            <h5>Student Information</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold">Student ID:</td>
                                    <td>{{ $user->student->student_id }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Course:</td>
                                    <td>{{ $user->student->course }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Department:</td>
                                    <td>{{ $user->student->department }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Year Level:</td>
                                    <td>{{ $user->student->year_level }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Mobile:</td>
                                    <td>{{ $user->student->mobile_number }}</td>
                                </tr>
                            </table>
                        </div>
                        @endif

                        @if($user->role_id == 2 && $user->registrar)
                        <div class="col-md-6">
                            <h5>Registrar Information</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold">Window Number:</td>
                                    <td>{{ $user->registrar->window_number }}</td>
                                </tr>
                            </table>
                        </div>
                        @endif
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-warning btn-sm edit-btn"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editUserModal{{ $user->id }}">
                                    <i class="fas fa-edit"></i> Edit User
                                </button>

                                <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Are you sure you want to delete this user?')">
                                        <i class="fas fa-trash"></i> Delete User
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    @include('admin.users.modals.edit', ['user' => $user])
</div>
@endsection