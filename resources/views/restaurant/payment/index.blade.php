@extends('dashboard.restaurantDashboard')

@section('title', 'Menu Management')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>Financial Menu</h2>
                <div class="card border shadow-sm p-3 mb-4">
                    <div
                        class="card-body d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                        <div>
                            <h4 class="">Current Balance</h4>
                            <h2 class="fw-bold text-success">Rp {{ number_format($totalAmount) }}</h2>
                        </div>
                        <button class="btn btn-danger mt-3 mt-md-0">Withdraw</button>
                    </div>
                </div>
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-body border">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="card-title mb-0">Incoming Payment</h5>
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
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($payments as $index => $payment)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $payment->reservationDate }}</td>
                                            <td>{{ $payment->reservationTime }}</td>
                                            <td class="fw-bold text-success">+ Rp{{ number_format($payment->priceTotal) }}
                                            </td>
                                            <td>
                                                @php
                                                    $statusMapping = [
                                                        'On Going' => 'Waiting',
                                                        'Arrived' => 'Waiting',
                                                        'Cancelled' => 'Approved',
                                                        'Finished' => 'Approved',
                                                    ];
                                                @endphp
                                                <span
                                                    class="badge
                                                {{ in_array($payment->reservationStatus, ['On Going', 'Arrived'])
                                                    ? 'bg-warning'
                                                    : ($payment->reservationStatus == 'Finished' || 'Cancelled'
                                                        ? 'bg-success'
                                                        : 'bg-secondary') }}">
                                                    {{ in_array($payment->reservationStatus, ['On Going', 'Arrived']) ? 'Waiting' : 'Approved' }}
                                                </span>

                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
