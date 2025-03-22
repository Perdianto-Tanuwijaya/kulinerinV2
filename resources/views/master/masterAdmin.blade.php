<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
                class="nav-link {{ Request::routeIs('adminDashboard') ? 'active' : '' }}">
                <i class="bi bi-house-door"></i>
                <span class="nav-text">Home</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('reward.index') }}"
                class="nav-link {{ Request::routeIs('reward.index') ? 'active' : '' }}">
                <i class="bi bi-list"></i>
                <span class="nav-text">Reward</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('point.index') }}" class="nav-link {{ Request::routeIs('point.index') ? 'active' : '' }}">
                <i class="bi bi-table"></i>
                <span class="nav-text">Point</span>
            </a>
        </li>
        <li class="nav-item">
            {{-- <a href="{{ route('') }}" class="nav-link {{ Request::routeIs('') ? 'active' : '' }}"> --}}
            <i class="bi bi-cart"></i>
            <span class="nav-text">Reservation</span>
            </a>
        </li>
        <li class="nav-item">
            {{-- <a href="{{ route('payment') }}" class="nav-link {{ Request::routeIs('payment') ? 'active' : '' }}"> --}}
            <i class="bi bi-credit-card"></i>
            <span class="nav-text">Payment</span>
            </a>
        </li>
        <li class="nav-item">
            {{-- <a href="{{ route('report') }}" class="nav-link {{ Request::routeIs('report') ? 'active' : '' }}"> --}}
            <i class="bi bi-file-text"></i>
            <span class="nav-text">Transaction Report</span>
            </a>
        </li>
        <li class="nav-item">
            {{-- <a href="{{ route('settings') }}" class="nav-link {{ Request::routeIs('settings') ? 'active' : '' }}"> --}}
            <i class="bi bi-gear"></i>
            <span class="nav-text">Settings</span>
            </a>
        </li>
        <li class="nav-item sign-out">
            <a href="{{ route('logoutAdmin') }}" class="nav-link">
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
