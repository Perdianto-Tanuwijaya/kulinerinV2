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
                        <button class="btn btn-danger mt-3 mt-md-0" data-bs-toggle="modal" data-bs-target="#withdrawModal">
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
                    </div>
                    <div class="pagination-container pagination-right mt-4">
                        {{ $payments->appends(['tab' => 'incomingPayments'])->links('pagination::bootstrap-5') }}
                    </div>
                </div>

                <div id="withdrawHistory" class="tab-content {{ request('tab') === 'withdrawHistory' ? 'active' : '' }}">
                    <div class="history-list">
                    </div>
                    <div class="pagination-container pagination-right mt-4">
                        {{ $withdraws->appends(['tab' => 'withdrawhistory'])->links('pagination::bootstrap-5') }}
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
                            <input type="number" class="form-control" id="withdrawAmount" name="amount" required
                                min="10000">
                        </div>
                        <div class="mb-3">
                            <label for="bankName" class="form-label">Bank Name</label>
                            <input type="text" class="form-control" id="bankName" name="bank_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="bankAccount" class="form-label">Bank Account</label>
                            <input type="text" class="form-control" id="bankAccount" name="bank_account" required>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Withdraw</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.tab').forEach(tab => {
            tab.addEventListener('click', function() {
                const tabName = this.getAttribute('data-tab');
                // Update URL dengan query parameter tab
                const url = new URL(window.location);
                url.searchParams.set('tab', tabName);
                window.history.pushState({}, '', url);

                // Aktivasi tab
                document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));

                this.classList.add('active');
                document.getElementById(tabName).classList.add('active');
            });
        });
        document.querySelectorAll('.filter-btn').forEach(button => {
            button.addEventListener('click', function() {
                const selectedCategory = this.getAttribute('data-category');

                // Perbarui URL agar tetap bisa diakses
                const url = new URL(window.location.href);
                url.searchParams.set('category', selectedCategory);
                window.history.pushState({}, '', url);

                // Perbarui tampilan filter aktif
                document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');

                // Filter reward berdasarkan kategori
                document.querySelectorAll('.reward-card').forEach(card => {
                    const rewardCategory = card.getAttribute('data-category');
                    if (selectedCategory === 'all' || rewardCategory === selectedCategory) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('.tab');

            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    // Remove active class from all tabs
                    tabs.forEach(t => t.classList.remove('active'));

                    // Add active class to clicked tab
                    this.classList.add('active');

                    // Hide all tab content
                    const tabContents = document.querySelectorAll('.tab-content');
                    tabContents.forEach(content => content.classList.remove('active'));

                    // Show the corresponding tab content
                    const tabId = this.getAttribute('data-tab');
                    document.getElementById(tabId).classList.add('active');
                });
            });
        });
    </script>
@endsection
