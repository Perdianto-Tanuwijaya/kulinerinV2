<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('css/restaurant.css') }}" rel="stylesheet">
    {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"> --}}

</head>
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <h5 class="nav-text">Dashboard</h5>
    </div>

    <button class="toggle-btn" onclick="toggleSidebar()">
        <i class="bi bi-chevron-left" id="toggle-icon"></i>
    </button>

    <ul class="nav flex-column">
        <li class="nav-item">
            <a href="{{ route('restaurantDashboard') }}"
                class="nav-link {{ Request::routeIs('restaurantDashboard') ? 'active' : '' }}">
                <i class="bi bi-house-door"></i>
                <span class="nav-text">Home</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('menu.index') }}" class="nav-link {{ Request::routeIs('menu.index') ? 'active' : '' }}">
                <i class="bi bi-list"></i>
                <span class="nav-text">Menu</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('table.index') }}" class="nav-link {{ Request::routeIs('table.index') ? 'active' : '' }}">
                <i class="bi bi-table"></i>
                <span class="nav-text">Table</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('restaurant.reservations') }}"
                class="nav-link {{ Request::routeIs('restaurant.reservations') ? 'active' : '' }}">
                <i class="bi bi-cart"></i>
                <span class="nav-text">Reservation</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('payment.index') }}"
                class="nav-link {{ Request::routeIs('payment.index') ? 'active' : '' }}">
                <i class="bi bi-credit-card"></i>
                <span class="nav-text">Financial</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('restaurantReport') }}" class="nav-link {{ Request::routeIs('restaurantReport') ? 'active' : '' }}">
                <i class="bi bi-file-text"></i>
                <span class="nav-text">Transaction Report</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('settings') }}" class="nav-link {{ Request::routeIs('settings') ? 'active' : '' }}">
                <i class="bi bi-gear"></i>
                <span class="nav-text">Settings</span>
            </a>
        </li>
        <li class="nav-item sign-out">
            <a href="{{ route('logoutRestaurant') }}" class="nav-link">
                <i class="bi bi-box-arrow-right"></i>
                <span class="nav-text">Sign Out</span>
            </a>
        </li>
    </ul>
</div>

<style>
    .sidebar {
        display: flex;
        flex-direction: column;
        height: 100vh;
    }

    .nav {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .sign-out {
        margin-top: auto;
    }
</style>