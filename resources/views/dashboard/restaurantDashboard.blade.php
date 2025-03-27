<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard')</title>
    <link rel="icon" href="{{ asset('asset/kulinerinLogo.png') }}" type="image/png">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        #create_restaurant_form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 280px;
            background-color: #333;
            padding: 20px;
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .sidebar.collapsed {
            width: 80px;
        }

        .sidebar-header {
            color: white;
            margin-bottom: 20px;
            white-space: nowrap;
            overflow: hidden;
        }

        .nav-link {
            color: #fff;
            padding: 12px 15px;
            border-radius: 5px;
            margin-bottom: 5px;
            white-space: nowrap;
            overflow: hidden;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-link:hover {
            background-color: #444;
            color: #fff;
        }

        .nav-link.active {
            background-color: #007bff;
        }

        .nav-text {
            opacity: 1;
            transition: opacity 0.3s ease;
        }

        .collapsed .nav-text {
            opacity: 0;
            display: none;
        }

        .toggle-btn {
            position: absolute;
            right: -15px;
            top: 20px;
            background: #fff;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .main-content {
            margin-left: 280px;
            padding: 20px;
            transition: margin-left 0.3s ease;
        }

        .main-content.expanded {
            margin-left: 80px;
        }

        .input-group input,
        textarea {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
        }

        #settings_form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }


        @media (max-width: 768px) {
            .sidebar {
                width: 80px;
            }

            .nav-text {
                opacity: 0;
                display: none;
            }

            .main-content {
                margin-left: 80px;
            }

            .sidebar.expanded {
                width: 280px;
            }

            .sidebar.expanded .nav-text {
                opacity: 1;
                display: inline;
            }
        }
    </style>
    @stack('styles')
</head>

<body>

    @include('master.masterRestaurant')

    <div class="main-content" id="main">
        @yield('content')
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main');
            const toggleIcon = document.getElementById('toggle-icon');

            if (window.innerWidth <= 768) {
                sidebar.classList.toggle('expanded');
            } else {
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
            }

            toggleIcon.classList.toggle('bi-chevron-left');
            toggleIcon.classList.toggle('bi-chevron-right');
        }

        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    document.getElementById('sidebar').classList.remove('expanded');
                }
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>
