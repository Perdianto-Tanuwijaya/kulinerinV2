@extends('dashboard.adminDashboard')

@section('title', 'Reward Management')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>Manage Reward</h2>
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-body border">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="card-title mb-0">Reward List</h5>
                            <a href="#" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#addRewardModal">
                                <i class="bi bi-plus"></i> Add Reward
                            </a>
                        </div>
                        <hr>
                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Reward Name</th>
                                        <th>Image</th>
                                        <th>Category</th>
                                        <th>Stock</th>
                                        <th>Points</th>
                                        <th>Is Active</th>
                                        <th>Description</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($rewards->isEmpty())
                                        <tr>
                                            <td colspan="8" class="text-center">No reward found.</td>
                                        </tr>
                                    @else
                                        @foreach ($rewards as $reward)
                                            <tr>
                                                <td>{{ $reward->name }}</td>
                                                <td>
                                                    @if ($reward->image)
                                                        <img src="{{ asset('storage/' . $reward->image) }}" width="50"
                                                            height="50" alt="Reward Image">
                                                    @else
                                                        No Image
                                                    @endif
                                                </td>
                                                <td>{{ $reward->category }}</td>
                                                <td>{{ $reward->stock }}</td>
                                                <td>{{ $reward->points }}</td>
                                                <td>
                                                    <span
                                                        class="badge {{ $reward->is_active ? 'bg-success' : 'bg-danger' }}">
                                                        {{ $reward->is_active ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </td>
                                                <td>{{ $reward->description ?? 'No description' }}</td>
                                                <td>
                                                    <div class="d-flex gap-1">
                                                        <!-- Tombol Edit -->
                                                        <a href="#" class="btn btn-sm btn-warning"
                                                            data-bs-toggle="modal" data-bs-target="#editRewardModal"
                                                            data-id="{{ $reward->id }}">
                                                            <i class="bi bi-pencil-square"></i> Edit
                                                        </a>

                                                        <!-- Form Hapus -->
                                                        <form class="delete-form d-inline"
                                                            action="{{ route('reward.destroy', $reward->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger delete-btn">
                                                                <i class="bi bi-trash"></i> Delete
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                            @if ($rewards->isEmpty())
                                                <div class="text">
                                                    <p>No reward found.</p>
                                                </div>
                                            @endif
                                        @endforeach
                                    @endforelse
                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        //AJAX FOR DELETE
        $(document).ready(function() {
            $(".delete-btn").click(function(e) {
                e.preventDefault();

                let btn = $(this);
                let form = btn.closest("form");
                let url = form.attr("action");
                // console.log(url);
                let token = $('meta[name="csrf-token"]').attr("content");

                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: url,
                            type: "POST",
                            data: {
                                _token: token,
                                _method: "DELETE"
                            },
                            success: function(response) {
                                Swal.fire("Deleted!", "The item has been deleted.",
                                        "success")
                                    .then(() => location
                                        .reload());
                            },
                            error: function(xhr) {
                                Swal.fire("Error!", "Failed to delete item.", "error");
                                console.error(xhr.responseText);
                            }
                        });
                    }
                });
            });
        });

        //
    </script>
    @include('admin.reward.createReward')
    @include('admin.reward.updateReward')
@endsection
