@extends('dashboard.restaurantDashboard')

@section('title', 'Financial')

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
                            <h2 class="fw-bold text-success">Rp {{ number_format($balance ?? 0) }}</h2>
                        </div>
                        <button class="btn text-white mt-3 mt-md-0" style="background-color: #D67B47ff" data-bs-toggle="modal"
                            data-bs-target="#withdrawModal">
                            Withdraw
                        </button>
                    </div>
                </div>
                <div class="tabs">
                    <div class="tab {{ request('tab', 'incomingPayments') === 'incomingPayments' ? 'active' : '' }}"
                        data-tab="incomingPayments">
                        Incoming Payments
                    </div>
                    <div class="tab {{ request('tab') === 'withdrawHistory' ? 'active' : '' }}" data-tab="withdrawHistory">
                        Withdraw History
                    </div>
                </div>

                <div id="incomingPayments"
                    class="tab-content {{ request('tab', 'incomingPayments') === 'incomingPayments' ? 'active' : '' }}">
                    <div class="rewards-grid">
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
                                            @if ($payments->isEmpty())
                                                <tr>
                                                    <td colspan="5" class="text-muted"
                                                        style="text-align: center; font-style: italic;">
                                                        No incoming payments found.
                                                    </td>
                                                </tr>
                                            @else
                                                @foreach ($payments as $index => $payment)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $payment->updated_at->format('d F Y') }}</td>
                                                        <td>{{ $payment->updated_at->format('H:i:s') }}</td>
                                                        <td class="fw-bold text-success">+
                                                            Rp{{ number_format($payment->priceTotal) }}
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
                                                                ? 'bg-warning text-dark'
                                                                : ($payment->reservationStatus == 'Finished' || $payment->reservationStatus == 'Cancelled'
                                                                    ? 'bg-success'
                                                                    : 'bg-secondary') }}">
                                                                {{ in_array($payment->reservationStatus, ['On Going', 'Arrived']) ? 'Waiting' : 'Approved' }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endforelse
                                        </tbody>

                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="pagination-container pagination-right mt-4">
                        {{ $payments->appends(['tab' => 'incomingPayments'])->links('pagination::bootstrap-5') }}
                    </div>
                </div>

                <div id="withdrawHistory" class="tab-content {{ request('tab') === 'withdrawHistory' ? 'active' : '' }}">
                    <div class="history-list">
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
                                            @if ($withdraws->isEmpty())
                                                <tr>
                                                    <td colspan="5" class="text-muted"
                                                        style="text-align: center; font-style: italic;">
                                                        No incoming payments found.
                                                    </td>
                                                </tr>
                                            @else
                                                @foreach ($withdraws as $index => $withdraw)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($withdraw->withdrawDate)->format('d F Y') }}
                                                        </td>
                                                        <td>{{ $withdraw->withdrawTime }}</td>
                                                        <td class="fw-bold text-danger">-
                                                            Rp{{ number_format($withdraw->amount) }}
                                                        </td>
                                                        <td>
                                                            @if ($withdraw->status == 'Pending')
                                                                <span class="badge bg-warning text-dark">Pending</span>
                                                            @elseif ($withdraw->status == 'Approved')
                                                                <span class="badge bg-success">Approved</span>
                                                            @elseif ($withdraw->status == 'Rejected')
                                                                <span class="badge bg-danger">Rejected</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="pagination-container pagination-right mt-4">
                        {{ $withdraws->appends(['tab' => 'withdrawHistory'])->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Withdraw -->
    <div class="modal fade" id="withdrawModal" tabindex="-1" aria-labelledby="withdrawModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="withdrawModalLabel">Withdraw Funds</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('payment.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="withdrawAmount" class="form-label">Amount (Rp)</label>
                            <input type="text" class="form-control" id="withdrawAmount" name="amount" required
                                min="10000">
                        </div>
                        <div class="mb-3">
                            <label for="bankName" class="form-label">Bank Name</label>
                            <input type="text" class="form-control" id="bankName" name="bankName" required>
                        </div>
                        <div class="mb-3">
                            <label for="bankAccount" class="form-label">Bank Account</label>
                            <input type="text" class="form-control" id="bankAccount" name="bankAccount" required>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn text-white"
                                style="background-color: #D67B47ff">Withdraw</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const withdrawAmount = document.getElementById("withdrawAmount");

            withdrawAmount.addEventListener("input", function() {
                let value = this.value.replace(/,/g, '').replace(/\D/g,
                    ''); // Hapus semua koma dan non-angka
                if (value) {
                    this.value = new Intl.NumberFormat("en-US").format(value);
                } else {
                    this.value = "";
                }
            });

            document.querySelector("form").addEventListener("submit", function() {
                withdrawAmount.value = withdrawAmount.value.replace(/,/g, ''); // Hapus koma sebelum submit
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('.tab');
            const tabContents = document.querySelectorAll('.tab-content');

            // Fungsi untuk mengatur parameter URL
            function updateUrlParams(tabName) {
                const url = new URL(window.location);

                // Set tab parameter
                url.searchParams.set('tab', tabName);

                // Hapus parameter page untuk mereset ke halaman 1
                url.searchParams.delete('page');

                // Update URL tanpa reload
                window.history.pushState({}, '', url);
            }

            // Fungsi untuk mengaktifkan tab
            function activateTab(tabName) {
                // Nonaktifkan semua tab dan konten
                tabs.forEach(t => t.classList.remove('active'));
                tabContents.forEach(c => c.classList.remove('active'));

                // Aktifkan tab dan konten yang sesuai
                document.querySelector(`.tab[data-tab="${tabName}"]`).classList.add('active');
                document.getElementById(tabName).classList.add('active');
            }

            // Event listener untuk setiap tab
            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    const tabName = this.getAttribute('data-tab');

                    // Update URL dan aktifkan tab
                    updateUrlParams(tabName);
                    activateTab(tabName);

                    // Reload halaman untuk mereset pagination
                    location.reload();
                });
            });

            // Inisialisasi tab aktif saat halaman dimuat
            const initialTab = new URL(window.location).searchParams.get('tab') || 'incomingPayments';
            activateTab(initialTab);
        });
    </script>
@endsection
