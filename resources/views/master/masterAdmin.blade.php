<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <h5 class="nav-text" style="color: black;">Dashboard Admin</h5>
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
                <i class="bi bi-gift"></i>
                {{-- <i class="bi bi-list"></i> --}}
                <span class="nav-text">Reward</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('point.index') }}" class="nav-link {{ Request::routeIs('point.index') ? 'active' : '' }}">
                <i class="bi bi-database-up"></i>
                {{-- <i class="bi bi-table"></i> --}}
                <span class="nav-text">Point</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('ad.index') }}" class="nav-link {{ Request::routeIs('ad.index') ? 'active' : '' }}">
                <i class="bi bi-badge-ad-fill"></i>
                {{-- <i class="bi bi-cart"></i> --}}
                <span class="nav-text">Advertisement</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('withdraw.show') }}"
                class="nav-link {{ Request::routeIs('withdraw.show') ? 'active' : '' }}">
                <i class="bi bi-credit-card"></i>
                <span class="nav-text">Payment</span>
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
        background-color: #D67B47ff;
    }

    .nav-link {
        color: white;
    }

    .nav-link.active {
        background-color: rgb(226, 152, 109) !important;
        /* Warna lebih gelap dari navbar */
        color: black !important;
        /* font-weight: bold; */
        border-radius: 5px;
        padding: 8px 12px;
        transition: background-color 0.3s ease, color 0.3s ease;
        /* Efek transisi halus */
    }

    .nav-link:hover {
        background-color: rgb(226, 152, 109) !important;
        /* Warna navbar asli */
        color: DECEB0ff !important;
        border-radius: 5px;
        transition: background-color 0.3s ease, color 0.3s ease;
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
