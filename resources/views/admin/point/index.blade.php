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
                                <button type="submit" class="btn btn-sm" style="background-color: #D67B47ff">
                                    <i class="bi bi-search text-white"></i>
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
                                            <td>
                                                @if ($point->pointLoyalty)
                                                    {{ number_format($point->pointLoyalty->point) }}
                                                @else
                                                    0
                                                @endif

                                            </td>
                                            <td>
                                                <a href="#" class="btn btn-sm text-white"
                                                    style="background-color: #D67B47ff" data-bs-toggle="modal"
                                                    data-bs-target="#updatePointModal" data-id="{{ $point->id }}">
                                                    <i class="bi bi-plus"></i>Add Points
                                                </a>
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
