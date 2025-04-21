@extends('dashboard.adminDashboard')

@section('title', 'Withdraw Management')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>Manage Withdraw</h2>
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-body border">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="card-title mb-0">Withdraw Request</h5>
                            <form action="{{ route('withdraw.show') }}" method="GET" class="d-flex"
                                style="max-width: 300px;">
                                <input type="text" name="search" class="form-control form-control-sm me-2"
                                    placeholder="Search restaurant..." value="{{ request('search') }}"
                                    style="width: 150px;">
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
                                        <th>No</th>
                                        <th>Restaurant Name</th>
                                        <th>Withdraw Date & Time</th>
                                        <th>Bank Name</th>
                                        <th>Bank Account</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($withdraws as $index => $wd)
                                        <tr>
                                            <td>{{ $loop->iteration }}.</td>
                                            <td>{{ $wd->restaurant->restaurantName ?? 'No Restaurant Name' }}</td>
                                            <td class="text-center">
                                                {{ \Carbon\Carbon::parse($wd->withdrawDate . ' ' . $wd->withdrawTime)->format('d F Y, H:i') }}
                                            </td>
                                            <td>{{ $wd->bankName }}</td>
                                            <td>{{ $wd->bankAccount }}</td>
                                            <td>Rp{{ number_format($wd->amount) }}</td>
                                            <td class="fst-italic">{{ $wd->status }}</td>
                                            <td>
                                                @if ($wd->status === 'Pending')
                                                    <button class="btn btn-sm btn-success approve-btn"
                                                        data-id="{{ $wd->id }}">Approve</button>
                                                    <button class="btn btn-sm btn-danger reject-btn"
                                                        data-id="{{ $wd->id }}">Reject</button>
                                                @else
                                                    <span
                                                        class="badge
                                                {{ $wd->status === 'Approved' ? 'bg-success' : ($wd->status === 'Rejected' ? 'bg-danger' : 'bg-secondary') }}">
                                                        {{ $wd->status }}
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">No withdrawals found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            <div class="pagination-container pagination-right mt-4">
                                {{ $withdraws->appends(['tab' => 'financial/withdraw'])->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Approve action
            document.querySelectorAll(".approve-btn").forEach(button => {
                button.addEventListener("click", function() {
                    let paymentId = this.getAttribute("data-id");

                    Swal.fire({
                        title: "Approve Withdraw?",
                        text: "This action will approve the withdrawal request.",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#28a745",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Yes, Approve!"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            updateStatus(paymentId, "Approved");
                        }
                    });
                });
            });

            // Reject action
            document.querySelectorAll(".reject-btn").forEach(button => {
                button.addEventListener("click", function() {
                    let paymentId = this.getAttribute("data-id");

                    Swal.fire({
                        title: "Reject Withdraw?",
                        text: "This action will reject the withdrawal request.",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#dc3545",
                        cancelButtonColor: "#6c757d",
                        confirmButtonText: "Yes, Reject!"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            updateStatus(paymentId, "Rejected");
                        }
                    });
                });
            });

            // Function to send AJAX request
            function updateStatus(id, status) {
                fetch(`/update-payment-status/${id}`, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute(
                                "content")
                        },
                        body: JSON.stringify({
                            status: status
                        })
                    })
                    .then(response => response.json())
                    //message setelah approve atau reject
                    .then(data => {
                        Swal.fire("Success!", `Withdraw has been ${status.toLowerCase()}!`, "success")
                            .then(() => location.reload());
                    })
                    .catch(error => {
                        Swal.fire("Error!", "Something went wrong!", "error");
                    });
            }
        });
    </script>

    {{-- @include('admin.point.updatePointUser') --}}
@endsection
