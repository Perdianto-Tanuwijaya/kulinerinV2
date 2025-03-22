<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Restaurant - Kuliner IN</title>
    <link rel="icon" href="{{ asset('asset/kulinerinLogo.png') }}" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link href="{{ asset('css/authStyle.css') }}" rel="stylesheet">
</head>

<body>
    @extends('toastr.message')
    <div class="container">
        <div class="logo-container">
            <img src="{{ asset('asset/kulinerinLogo.png') }}" alt="Logo">
        </div>
        <div class="login-container">
            <h1>Register Your Restaurant</h1>

            <form action="{{ url('registerrestaurant') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="input-group" style="display:none">
                    <input type="text" name="username" id="username" placeholder="Username" readonly required>
                </div>
                <div class="input-group" style="display:none">
                    <input type="text" name="password" id="password" placeholder="password" readonly required>
                </div>
                <div class="input-group" style="display:none">
                    <input type="text" name="email" id="email" placeholder="email" readonly required>
                </div>
                <div class="input-group">
                    <input type="text" name="name" id="name" placeholder="Restaurant Name" value="{{ old('name') }}" required>
                </div>

                <div class="input-group">
                    <input type="text" name="number" id="number" placeholder="Phone Number" value="{{ old('number') }}" required>
                </div>

                <div class="input-group">
                    <input type="text" name="city" id="city" placeholder="City" value="{{ old('city') }}" required>
                </div>

                <div class="input-group">
                    <input type="text" name="address" id="address" placeholder="Address" value="{{ old('address') }}" required>
                </div>

                <div class="input-group">
                    <textarea name="desc" id="desc" placeholder="Description" required>{{ old('desc') }}</textarea>
                </div>

                <div class="input-group">
                    <label for="style">Restaurant Category</label>
                    <select name="style" id="style" required>
                        <option value="Asian">Asian</option>
                        <option value="Western">Western</option>
                        <option value="Fine Dining">Fine Dining</option>
                        <option value="Bar">Bar</option>
                    </select>
                </div>

                <div class="input-group">
                    <label for="image">Upload Image (Min 3)</label>
                    <input type="file" name="image[]" id="image" accept="image/*" multiple required onchange="previewImages()">
                    <div id="imagePreviewContainer" style="display: flex; gap: 10px; margin-top: 10px;"></div>
                    <small id="imageError" style="color: red; display: none;">Please upload at least 3 images.</small>
                </div>

                <button type="submit" class="login-button">Register</button>
            </form>

            <form action="/register" method="GET">
                <button type="submit" class="google-button">Back</button>
            </form>
        </div>
    </div>


    <script>
        function checkImages() {
            console.log("Checking images...");
            let input = document.getElementById("image");
            let errorMsg = document.getElementById("errorMsg");

            if (input.files.length != 3) {
                alert("Please select  3 images.");
                input.value = ""; // Reset file input
            } else {
                errorMsg.textContent = "";
            }
        }

        function validateImages() {
            let input = document.getElementById("imageUpload");
            let errorMsg = document.getElementById("errorMsg");

            if (input.files.length < 3) {
                errorMsg.textContent = "Please upload at least 3 images.";
                return false;
            }
            errorMsg.textContent = "";
            return true;
        }
    </script>

    <script>
        function getCookie(name) {
            let matches = document.cookie.match(new RegExp(
                "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
            ));
            return matches ? decodeURIComponent(matches[1]) : null;
        }

        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById("username").value = getCookie("temp_username") || "";
            document.getElementById("email").value = getCookie("temp_email") || "";
            document.getElementById("password").value = getCookie("temp_password") || "";
        });
    </script>
</body>


</html>