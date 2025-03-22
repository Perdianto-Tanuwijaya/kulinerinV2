@extends('dashboard.restaurantDashboard')

@section('title', 'Settings')

@section('content')
<div class="container">
    <h3>Welcome to Dashboard Settings</h3>


    <form id="create_restaurant_form" action="{{ route('restaurantCreation') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="input-group">
            <label for="name">Restaurant Name</label>
            <input type="text" name="name" id="name" placeholder="Restaurant Name" required>
        </div>

        <div class="input-group">
            <label for="number">Restaurant Phone Number</label>
            <input type="text" name="number" id="number" placeholder="Phone Number" required>
        </div>

        <div class="input-group">
            <label for="city">Restaurant City</label>
            <input type="text" name="city" id="city" placeholder="City" required>
        </div>

        <div class="input-group">
            <label for="address">Restaurant Address</label>
            <input type="text" name="address" id="address" placeholder="Address" required>
        </div>

        <div class="input-group">
            <label for="desc">Restaurant Description</label>
            <textarea name="desc" id="desc" placeholder="Description" required></textarea>
        </div>


        <div class="input-group" style="display: grid;">
            <label for="style">Restaurant Category</label>
            <select name="style" id="style" required>
                <option value="Asian">Asian</option>
                <option value="Western">Western</option>
                <option value="Fine Dining">Fine Dining</option>
                <option value="Bar">Bar</option>
            </select>
        </div>

        <div class="input-group" style="display: grid; gap:20px">
            <label>Restaurant Operating Hours</label>
            <div id="schedule-container">
                <div class="schedule-row">
                    <select name="days[]" class="days-dropdown" onchange="updateDaySelection()">
                        <option value="Monday">Monday</option>
                        <option value="Tuesday">Tuesday</option>
                        <option value="Wednesday">Wednesday</option>
                        <option value="Thursday">Thursday</option>
                        <option value="Friday">Friday</option>
                        <option value="Saturday">Saturday</option>
                        <option value="Sunday">Sunday</option>
                    </select>
                    <input type="time" name="open_time[]" class="open-time" required>
                    <span>to</span>
                    <input type="time" name="close_time[]" class="close-time" required>
                    <button type="button" class="remove-schedule">✖</button>
                </div>
            </div>
            <button type="button" style="width: min-content;background:transparent;color:#4286f5;text-decoration:underline; white-space:nowrap" id="add-schedule">Add </button>

        </div>

        <div class="input-group" style=" display: grid; height: min-content;">
            <label for="image">Update Images (Min 3)</label>

            <div id="imagePreviewContainer" style="display: flex; gap: 10px; margin-top: 10px;">
                @php
                $images = explode(',', '');
                $images = array_map('trim', $images);
                $maxSlots = 3; // Ensure 3 slots exist

                // Adjust array to always have exactly 3 slots
                for ($i = 0; $i < $maxSlots; $i++) {
                    if (!isset($images[$i])) {
                    $images[$i]=null; // Fill missing slots with null
                    }
                    }
                    @endphp

                    @foreach($images as $index=> $image)
                    <div class="image-wrapper">
                        <img src="{{ $image ? asset('storage/' . $image) : asset('storage/restaurant/default.jpg') }}"
                            class="preview-image" data-index="{{ $index }}" onclick="triggerFileInput({{ $index }})"
                            style="width: 100px; height: 100px; cursor: pointer; object-fit: cover;">

                        <!-- Hidden input to maintain null values -->
                        <input type="hidden" name="existing_images[{{ $index }}]" value="{{ $image ?? 'null' }}">

                        <input type="file" name="image[{{ $index }}]" class="image-input" data-index="{{ $index }}"
                            accept="image/*" style="display: none;" onchange="replaceImage(event, {{ $index }})">
                    </div>
                    @endforeach
            </div>

            <small id="imageError" style="color: red; display: none;">Please upload at least 3 images.</small>
            <button style="margin-top:20p" type="submit">Create Restaurant</button>

        </div>



        <div></div>
    </form>




</div>

<script>
    document.getElementById("add-schedule").addEventListener("click", function() {
        const container = document.getElementById("schedule-container");
        const selectedDays = [...document.querySelectorAll(".days-dropdown")].map(select => select.value);

        if (selectedDays.length >= 7) {
            alert("You cannot add more than 7 days.");
            return;
        }

        const availableDays = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"]
            .filter(day => !selectedDays.includes(day));

        if (availableDays.length === 0) {
            alert("No more days available to add.");
            return;
        }

        const newRow = document.createElement("div");
        newRow.classList.add("schedule-row");

        newRow.innerHTML = `
        <select name="days[]" class="days-dropdown" onchange="updateDaySelection()">
            ${availableDays.map(day => `<option value="${day}">${day}</option>`).join("")}
        </select>
        <input type="time" name="open_time[]" class="open-time" required>
        <span>to</span>
        <input type="time" name="close_time[]" class="close-time" required>
        <button type="button" class="remove-schedule">✖</button>
    `;

        container.appendChild(newRow);

        newRow.querySelector(".remove-schedule").addEventListener("click", function() {
            newRow.remove();
            updateDaySelection();
        });

        addTimeValidation(newRow);
    });

    function updateDaySelection() {
        const selectedDays = [...document.querySelectorAll(".days-dropdown")].map(select => select.value);
        document.querySelectorAll(".days-dropdown").forEach(select => {
            [...select.options].forEach(option => {
                option.disabled = selectedDays.includes(option.value) && option.value !== select.value;
            });
        });
    }

    function addTimeValidation(row) {
        const openTimeInput = row.querySelector(".open-time");
        const closeTimeInput = row.querySelector(".close-time");

        function validateTime() {
            if (openTimeInput.value && closeTimeInput.value && openTimeInput.value >= closeTimeInput.value) {
                alert("Invalid time selection! Opening time must be earlier than closing time.");
                closeTimeInput.value = "";
            }
        }

        openTimeInput.addEventListener("change", validateTime);
        closeTimeInput.addEventListener("change", validateTime);
    }

    document.querySelectorAll(".remove-schedule").forEach(button => {
        button.addEventListener("click", function() {
            this.parentElement.remove();
            updateDaySelection();
        });
    });

    // Add validation for the default row
    addTimeValidation(document.querySelector(".schedule-row"));
</script>


<script>
    function triggerFileInput(index) {
        document.querySelector(`input.image-input[data-index="${index}"]`).click();
    }

    function replaceImage(event, index) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.querySelector(`.preview-image[data-index="${index}"]`).src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    }
</script>

<style>
    .schedule-row {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 5px;
    }

    .days-dropdown {
        padding: 5px;
    }

    .remove-schedule {
        background: none;
        border: none;
        color: red;
        font-size: 16px;
        cursor: pointer;
    }

    #add-schedule {
        margin-top: 5px;
        background-color: #007bff;
        color: white;
        border: none;
        padding: 5px 10px;
        cursor: pointer;
    }
</style>
@endsection