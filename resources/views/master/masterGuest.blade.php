<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('asset/kulinerinLogo.png') }}" type="image/png">
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link href="{{ asset('css/navbar.css') }}" rel="stylesheet">
</head>

<body>
    <nav class="navbar">
        <a href="{{ route('guestDashboard') }}">
            <img src="{{ asset('asset/kulinerinLogo.png') }}" alt="Kulinerin Logo" class="logo">
        </a>

        <div class="search-container">
            <form action="{{ route('search') }}" method="GET">
                <input type="text" name="keyword" class="search-bar" placeholder="Search Any Restaurant Here"
                    value="{{ request()->query('keyword') }}">
            </form>
        </div>

        <!-- Hamburger Button -->
        <button id="hamburger-btn" class="hamburger">&#9776;</button>

        <!-- Desktop Nav Links -->
        <div class="nav-links desktop-only">
            <a href="/" class="nav-link">Dashboard</a>
            <a href="{{ route('login') }}" class="nav-link">Login</a>
            <a href="{{ route('register') }}" class="nav-link">Register</a>
        </div>

        <!-- Mobile Dropdown -->
        <div id="dropdown-menu" class="dropdown hidden">
            <a href="/" class="dropdown-link">Dashboard</a>
            <a href="{{ route('login') }}" class="dropdown-link">Login</a>
            <a href="{{ route('register') }}" class="dropdown-link">Register</a>
        </div>
    </nav>

    @yield('content')
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3 class="footer-title">About Kulinerin</h3>
                <div class="footer-links">
                    <a href="#" class="footer-link">About Us</a>
                    <a href="#" class="footer-link">Careers</a>
                    <a href="#" class="footer-link">Press Center</a>
                    <a href="#" class="footer-link">Blog</a>
                </div>
            </div>

            <div class="footer-section">
                <h3 class="footer-title">For Restaurants</h3>
                <div class="footer-links">
                    <a href="#" class="footer-link">Partner with Us</a>
                    <a href="#" class="footer-link">Restaurant Dashboard</a>
                    <a href="#" class="footer-link">Marketing Tools</a>
                    <a href="#" class="footer-link">Success Stories</a>
                </div>
            </div>

            <div class="footer-section">
                <h3 class="footer-title">Support</h3>
                <div class="footer-links">
                    <a href="#" class="footer-link">Help Center</a>
                    <a href="#" class="footer-link">Contact Us</a>
                    <a href="#" class="footer-link">Privacy Policy</a>
                    <a href="#" class="footer-link">Terms & Conditions</a>
                </div>
            </div>
            <div class="footer-section">
                <h3 class="footer-title">Download App</h3>
                <p style="color: #9ca3af; margin-bottom: 1rem;">Get the best restaurant experience on your phone</p>
                <div class="app-badges">
                    <a href="#" class="app-badge">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M17.05 12.59L15.14 14.5L17.05 16.41L18.46 15L16.55 13.09L18.46 11.18L17.05 9.77L15.14 11.68L13.23 9.77L11.82 11.18L13.73 13.09L11.82 15L13.23 16.41L15.14 14.5L17.05 12.59Z" />
                        </svg>
                        App Store
                    </a>
                    <a href="#" class="app-badge">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M3.9 12C3.9 10.29 4.75 8.8 6.07 7.88L7.88 10.78C7.67 11.13 7.55 11.54 7.55 12C7.55 12.46 7.67 12.87 7.88 13.22L6.07 16.12C4.75 15.2 3.9 13.71 3.9 12M12 3.9C12.46 3.9 12.87 4.02 13.22 4.23L16.12 2.42C15.2 1.1 13.71 0.25 12 0.25C8.13 0.25 4.9 3.48 4.9 7.35V16.65C4.9 20.52 8.13 23.75 12 23.75C13.71 23.75 15.2 22.9 16.12 21.58L13.22 19.77C12.87 19.98 12.46 20.1 12 20.1C9.97 20.1 8.3 18.43 8.3 16.4V7.6C8.3 5.57 9.97 3.9 12 3.9M20.1 12C20.1 13.71 19.25 15.2 17.93 16.12L16.12 13.22C16.33 12.87 16.45 12.46 16.45 12C16.45 11.54 16.33 11.13 16.12 10.78L17.93 7.88C19.25 8.8 20.1 10.29 20.1 12" />
                        </svg>
                        Google Play
                    </a>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="footer-social">
                <a href="#" class="footer-link">
                    <svg class="social-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                    </svg>
                </a>
                <a href="#" class="footer-link">
                    <svg class="social-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z" />
                    </svg>
                </a>
                <a href="#" class="footer-link">
                    <svg class="social-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z" />
                    </svg>
                </a>
            </div>
            <p>© 2025 Kulinerin. All rights reserved.</p>
        </div>
    </footer>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Include Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        $(document).ready(function() {
            // Display error messages if any
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    toastr.error("{{ $error }}");
                @endforeach
            @endif

            // Display success message if session has success message
            @if (session('success'))
                toastr.success("{{ session('success') }}");
            @endif
        });
        const hamburgerBtn = document.getElementById('hamburger-btn');
        const dropdown = document.getElementById('dropdown-menu');
        let menuOpen = false;

        hamburgerBtn.addEventListener('click', function() {
            menuOpen = !menuOpen;
            dropdown.classList.toggle('hidden');

            // Toggle isi tombol
            hamburgerBtn.innerHTML = menuOpen ? '&times;' : '&#9776;';
        });
    </script>


    @yield ('masterCustomer')
</body>

</html>
