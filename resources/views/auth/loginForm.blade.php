<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Kuliner IN</title>
    <link rel="icon" href="{{ asset('asset/kulinerinLogo.png') }}" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link href="{{ asset('css/authStyle.css') }}" rel="stylesheet">
</head>

<body>
    @extends('toastr.message')
    <div class="container">
        <!-- Logo is placed outside the login form box -->
        <div class="logo-container">
            <img src="{{ asset('asset/kulinerinLogo.png') }}" alt="Logo">
        </div>
        <!-- Login form box starts here -->
        <div class="login-container">
            <h1>Login</h1>

            <!-- Login form -->
            <div>
                <form action="{{ route('login') }}" method="POST">
                    @csrf
                    <div class="input-group">
                        <input type="email" name="email" id="email" placeholder="Email"
                            value="{{ old('email') }}" required>
                    </div>

                    <div class="input-group">
                        <input type="password" name="password" id="password" placeholder="Password" required>
                    </div>

                    <button type="submit" class="login-button">Login</button>
                </form>

                <!-- Google Sign Up button -->
                <form action="{{ route('google.redirect') }}" method="GET">
                    <button type="submit" class="google-button">
                        <img src="{{ asset('asset/logoGoogle.png') }}" alt="Google Logo" class="google-logo" />
                        Sign Up with Google
                    </button>
                </form>
            </div>

            <div class="signup-link">
                <p>Don't have an account? <a href="/register">Sign up</a></p>
            </div>
        </div>
    </div>

</body>

</html>
