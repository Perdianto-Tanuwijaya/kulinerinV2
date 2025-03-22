@extends('dashboard.adminDashboard')

@section('title', 'Point Management')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>Manage User Points</h2>
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-body border">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="card-title mb-0">User List</h5>
                            <form action="{{ route('point.index') }}" method="GET" class="d-flex" style="max-width: 300px;">
                                <input type="text" name="search" class="form-control form-control-sm me-2"
                                    placeholder="Search username..." value="{{ request('search') }}" style="width: 150px;">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="bi bi-search"></i>
                                </button>
                            </form>
                        </div>
                        <hr>
                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>User ID</th>
                                        <th>Username</th>
                                        <th>Total Points</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($pointUser as $point)
                                        <tr>
                                            <td>{{ $point->id }}</td>
                                            <td>{{ $point->username ?? 'No Username' }}</td>
                                            <td>{{ number_format($point->pointLoyalties->point ?? 0) }}</td>
                                            <td>
                                                <a href="#" class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                                    data-bs-target="#updatePointModal" data-id="{{ $point->id }}">
                                                    <i class="bi bi-plus"></i>Add Points
                                                </a>
                                                {{-- <button class="btn btn-primary btn-sm editPointBtn"
                                                    data-id="{{ $point->id }}" data-username="{{ $point->username }}"
                                                    data-points="{{ $point->point }}" data-bs-toggle="modal"
                                                    data-bs-target="#updatePointModal">
                                                    Edit
                                                </button> --}}

                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">No user found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('admin.point.updatePointUser')
@endsection
