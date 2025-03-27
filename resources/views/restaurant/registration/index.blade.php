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

            </div>
            <button type="button" id="add-schedule" style="width: min-content; background:transparent; color:#4286f5; text-decoration:underline; white-space:nowrap">Add</button>
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
            <button style="margin-top:20px" id="createRestaurant" type="submit">Create Restaurant</button>

        </div>



        <div></div>
    </form>




</div>

<script>
    function generateTimeOptions(selectedValue = "") {
        let options = "";
        for (let h = 0; h < 24; h++) {
            for (let m = 0; m < 60; m += 30) {
                let hour = h.toString().padStart(2, "0");
                let minute = m.toString().padStart(2, "0");
                let time = `${hour}:${minute}`;
                let selected = time === selectedValue ? "selected" : "";
                options += `<option value="${time}" ${selected}>${time}</option>`;
            }
        }
        return options;
    }

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
    <select name="open_time[]" class="open-time" required>${generateTimeOptions()}</select>
    <span>to</span>
    <select name="close_time[]" class="close-time" required>${generateTimeOptions("23:30")}</select>
    <button type="button" class="remove-schedule">✖</button>
`;

        container.appendChild(newRow);

        newRow.querySelector(".remove-schedule").addEventListener("click", function() {
            newRow.remove();
            updateDaySelection();
        });

        addTimeValidation(newRow);
    });

    function addTimeValidation(row) {
        const openTimeSelect = row.querySelector(".open-time");
        const closeTimeSelect = row.querySelector(".close-time");

        function validateTime() {
            if (openTimeSelect.value && closeTimeSelect.value && openTimeSelect.value >= closeTimeSelect.value) {
                alert("Invalid time selection! Opening time must be earlier than closing time.");
                closeTimeSelect.value = "23:30"; // Reset to default if invalid
            }
        }

        openTimeSelect.addEventListener("change", validateTime);
        closeTimeSelect.addEventListener("change", validateTime);
    }

    document.querySelectorAll(".schedule-row").forEach(addTimeValidation);
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
    #createRestaurant {
        width: 100%;
        background-color: #007bff;
        color: white;
        border: none;
        padding: 10px 20px;
        font-size: 16px;
        border-radius: 4px;
        cursor: pointer;
        transition: background 0.2s;
    }

    .schedule-row {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 8px;
        background: #f9f9f9;
        border-radius: 6px;
    }

    .days-dropdown,
    .open-time,
    .close-time {
        padding: 6px;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 14px;
    }

    .remove-schedule {
        background: none;
        border: none;
        color: #d9534f;
        font-size: 18px;
        cursor: pointer;
        transition: color 0.2s;
    }

    .remove-schedule:hover {
        color: #c9302c;
    }

    #add-schedule {
        margin-top: 8px;
        background-color: #007bff;
        color: white;
        border: none;
        padding: 6px 12px;
        font-size: 14px;
        border-radius: 4px;
        cursor: pointer;
        transition: background 0.2s;
    }

    #add-schedule:hover {
        background-color: #0056b3;
    }
</style>
@endsection