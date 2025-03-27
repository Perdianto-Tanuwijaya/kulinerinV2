@extends('dashboard.restaurantDashboard')

@section('title', 'Reports')

@section('content')

<div class="container">
    <h2>{{ $restaurant->name }} Reservation Report</h2>
    <p>View reservations for your restaurant.</p>

    <!-- Date Range Filter Form -->
    <form method="GET" action="{{ route('restaurantReport') }}" class="mb-4">
        <div class="row">
            <div class="col-md-4">
                <label for="start_date">Start Date</label>
                <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
            </div>
            <div class="col-md-4">
                <label for="end_date">End Date</label>
                <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" name="export" value="1" class="btn btn-success">Export CSV</button>
            </div>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User ID</th>
                    <th>Guests</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Status</th>
                    <th>Booking Code</th>
                    <th>Menu</th>
                    <th>Total Price</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($reservations as $reservation)
                <tr>
                    <td>{{ $reservation->id }}</td>
                    <td>{{ $reservation->user_id }}</td>
                    <td>{{ $reservation->guest }}</td>
                    <td>{{ $reservation->reservationDate }}</td>
                    <td>{{ $reservation->reservationTime }}</td>
                    <td>{{ $reservation->reservationStatus }}</td>
                    <td>{{ $reservation->bookingCode }}</td>
                    <td>{{ $reservation->menuData ?? '-' }}</td>
                    <td>{{ $reservation->priceTotal ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="9">No reservations found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection