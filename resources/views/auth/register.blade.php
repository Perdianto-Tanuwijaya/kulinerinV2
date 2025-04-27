<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Kuliner IN</title>
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
            <h1>Register</h1>

            <!-- Login form -->
            <!-- Combined Register Form -->
            <form action="{{ url('register') }}" method="POST">
                @csrf
                <div class="input-group">
                    <input type="text" name="username" id="username" placeholder="Username"
                        value="{{ old('username') }}" required>
                </div>
                <div class="input-group">
                    <input type="email" name="email" id="email" placeholder="Email" value="{{ old('email') }}"
                        required>
                </div>

                <div class="input-group">
                    <input type="password" name="password" id="password" placeholder="Password" required>
                </div>

                <div class="input-group">
                    <input type="password" name="confirmation_password" id="confirmation_password"
                        placeholder="Confirm Password" required>
                </div>

                <!-- Hidden input to indicate user type -->
                <input type="hidden" name="register_as_restaurant" id="register_as_restaurant" value="0">

                <button type="submit" class="login-button">Register</button>
                <button type="button" class="google-button" id="restaurantRegister">Register as Restaurant</button>
            </form>

            <div class="signup-link">
                <p>Already have an account? <a href="/login">Log In</a></p>
            </div>

            <script>
                document.getElementById("restaurantRegister").addEventListener("click", function() {
                    document.getElementById("register_as_restaurant").value = "1"; // Mark as restaurant registration
                    document.querySelector("form").submit();
                });
            </script>

        </div>
    </div>

</body>

</html>
