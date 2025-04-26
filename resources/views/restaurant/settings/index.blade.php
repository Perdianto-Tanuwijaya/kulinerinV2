@extends('dashboard.restaurantDashboard')

@section('title', 'Settings')

@section('content')
<div class="container">
    <h3>Welcome to Dashboard Settings, {{ $restaurant->restaurantName }}</h3>

    <div id="settings_form">

        <div class="input-group">
            <label for="name">Restaurant Name</label>
            <input type="text" name="name" id="name" placeholder="Restaurant Name"
                value="{{ old('name', $restaurant->restaurantName) }}" required disabled>
        </div>

        <div class="input-group">
            <label for="number">Restaurant Phone Number</label>
            <input type="text" name="number" id="number" placeholder="Phone Number"
                value="{{ old('number', $restaurant->restaurantPhoneNumber) }}" required disabled>
        </div>

        <div class="input-group">
            <label for="city">Restaurant City</label>
            <input type="text" name="city" id="city" placeholder="City"
                value="{{ old('city', $restaurant->restaurantCity) }}" required disabled>
        </div>

        <div class="input-group">
            <label for="address">Restaurant Address</label>
            <input type="text" name="address" id="address" placeholder="Address"
                value="{{ old('address', $restaurant->restaurantAddress) }}" required disabled>
        </div>

        <div class="input-group">
            <label for="desc">Restaurant Description</label>
            <textarea name="desc" id="desc" placeholder="Description" required disabled>{{ old('desc', $restaurant->restaurantDescription) }}</textarea>
        </div>

        <div class="input-group" style="display: grid;">
            <label for="style">Restaurant Category</label>
            <select name="style" id="style" required disabled>
                <option value="Asian" {{ $restaurant->restaurantStyle == 'Asian' ? 'selected' : '' }}>Asian</option>
                <option value="Western" {{ $restaurant->restaurantStyle == 'Western' ? 'selected' : '' }}>Western</option>
                <option value="Fine Dining" {{ $restaurant->restaurantStyle == 'Fine Dining' ? 'selected' : '' }}>Fine Dining</option>
                <option value="Bar" {{ $restaurant->restaurantStyle == 'Bar' ? 'selected' : '' }}>Bar</option>
            </select>
        </div>

        <div class="input-group" style="display: grid; gap:20px">
            <label>Restaurant Operating Hours</label>
            <div>
                @foreach ($schedules as $index => $schedule)
                <div class="schedule-row">
                    <select name="days[]" class="days-dropdown" disabled>
                        @php $selectedDay = $schedule['day']; @endphp
                        @foreach (["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"] as $day)
                        <option value="{{ $day }}" {{ $selectedDay == $day ? 'selected' : '' }}>{{ $day }}</option>
                        @endforeach
                    </select>
                    <select name="open_time[]" class="open-time" required disabled>
                        @php
                        $dbOpenTime = date('H:i', strtotime($schedule['open_time'])); // Convert DB time format
                        @endphp
                        @foreach (range(0, 23) as $hour)
                        @foreach ([0, 30] as $minute)
                        @php
                        $time = sprintf('%02d:%02d', $hour, $minute);
                        $selected = ($dbOpenTime == $time) ? 'selected' : '';
                        @endphp
                        <option value="{{ $time }}" {{ $selected }}>{{ $time }}</option>
                        @endforeach
                        @endforeach
                    </select>
                    <span>to</span>
                    <select name="close_time[]" class="close-time" required disabled>
                        @php
                        $dbCloseTime = date('H:i', strtotime($schedule['close_time'])); // Convert DB time format
                        @endphp
                        @foreach (range(0, 23) as $hour)
                        @foreach ([0, 30] as $minute)
                        @php
                        $time = sprintf('%02d:%02d', $hour, $minute);
                        $selected = ($dbCloseTime == $time) ? 'selected' : '';
                        @endphp
                        <option value="{{ $time }}" {{ $selected }}>{{ $time }}</option>
                        @endforeach
                        @endforeach
                    </select>
                </div>
                @endforeach
            </div>
        </div>







        <div class="input-group" style=" display: grid;">
            <label for="image">Update Images (Min 3)</label>

            <div id="imagePreviewContainer" style="display: flex; gap: 10px; margin-top: 10px;">
                @php
                $images = explode(',', $restaurant->restaurantImage);
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
                            class="preview-image" data-index="{{ $index }}"
                            style=" width: 100px; height: 100px; cursor: pointer; object-fit: cover;">

                        <!-- Hidden input to maintain null values -->
                        <input type="hidden" name="existing_images[{{ $index }}]" value="{{ $image ?? 'null' }}">

                        <input type="file" name="image[{{ $index }}]" class="image-input" data-index="{{ $index }}"
                            accept="image/*" style="display: none;" onchange="replaceImage(event, {{ $index }})" disabled>
                    </div>
                    @endforeach
            </div>

            <small id="imageError" style="color: red; display: none;">Please upload at least 3 images.</small>
        </div>



    </div>
    <button type="button" id="openEditModal">Edit Restaurant</button>



    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3>Edit Restaurant Details</h3>
            <form id="settings_form" action="{{ route('restaurant.update', $restaurant->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="input-group">
                    <label for="name">Restaurant Name</label>
                    <input type="text" name="name" id="name" placeholder="Restaurant Name"
                        value="{{ old('name', $restaurant->restaurantName) }}" required>
                </div>

                <div class="input-group">
                    <label for="number">Restaurant Phone Number</label>
                    <input type="text" name="number" id="number" placeholder="Phone Number"
                        value="{{ old('number', $restaurant->restaurantPhoneNumber) }}" required>
                </div>

                <div class="input-group">
                    <label for="city">Restaurant City</label>
                    <input type="text" name="city" id="city" placeholder="City"
                        value="{{ old('city', $restaurant->restaurantCity) }}" required>
                </div>

                <div class="input-group">
                    <label for="address">Restaurant Address</label>
                    <input type="text" name="address" id="address" placeholder="Address"
                        value="{{ old('address', $restaurant->restaurantAddress) }}" required>
                </div>

                <div class="input-group">
                    <label for="desc">Restaurant Description</label>
                    <textarea name="desc" id="desc" placeholder="Description" required>{{ old('desc', $restaurant->restaurantDescription) }}</textarea>
                </div>

                <div class="input-group" style="display: grid;">
                    <label for="style">Restaurant Category</label>
                    <select name="style" id="style" required>
                        <option value="Asian" {{ $restaurant->restaurantStyle == 'Asian' ? 'selected' : '' }}>Asian</option>
                        <option value="Western" {{ $restaurant->restaurantStyle == 'Western' ? 'selected' : '' }}>Western</option>
                        <option value="Fine Dining" {{ $restaurant->restaurantStyle == 'Fine Dining' ? 'selected' : '' }}>Fine Dining</option>
                        <option value="Bar" {{ $restaurant->restaurantStyle == 'Bar' ? 'selected' : '' }}>Bar</option>
                    </select>
                </div>

                <div class="input-group" style="display: grid; gap:20px">
                    <label>Restaurant Operating Hours</label>
                    <div id="schedule-container">
                        @foreach ($schedules as $index => $schedule)
                        <div class="schedule-row">
                            <select name="days[]" class="days-dropdown">
                                @php $selectedDay = $schedule['day']; @endphp
                                @foreach (["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"] as $day)
                                <option value="{{ $day }}" {{ $selectedDay == $day ? 'selected' : '' }}>{{ $day }}</option>
                                @endforeach
                            </select>
                            <select name="open_time[]" class="open-time" required>
                                @php
                                $dbOpenTime = date('H:i', strtotime($schedule['open_time'])); // Convert DB time format
                                @endphp
                                @foreach (range(0, 23) as $hour)
                                @foreach ([0, 30] as $minute)
                                @php
                                $time = sprintf('%02d:%02d', $hour, $minute);
                                $selected = ($dbOpenTime == $time) ? 'selected' : '';
                                @endphp
                                <option value="{{ $time }}" {{ $selected }}>{{ $time }}</option>
                                @endforeach
                                @endforeach
                            </select>
                            <span>to</span>
                            <select name="close_time[]" class="close-time" required>
                                @php
                                $dbCloseTime = date('H:i', strtotime($schedule['close_time'])); // Convert DB time format
                                @endphp
                                @foreach (range(0, 23) as $hour)
                                @foreach ([0, 30] as $minute)
                                @php
                                $time = sprintf('%02d:%02d', $hour, $minute);
                                $selected = ($dbCloseTime == $time) ? 'selected' : '';
                                @endphp
                                <option value="{{ $time }}" {{ $selected }}>{{ $time }}</option>
                                @endforeach
                                @endforeach
                            </select>
                            <button type="button" class="remove-schedule">✖</button>
                        </div>
                        @endforeach
                    </div>
                    <button type="button" id="add-schedule" style="width: min-content; background:transparent; color:#4286f5; text-decoration:underline; white-space:nowrap">Add</button>
                </div>







                <div class="input-group" style=" display: grid;">
                    <label for="image">Update Images (Min 3)</label>

                    <div id="imagePreviewContainer" style="display: flex; gap: 10px; margin-top: 10px;">
                        @php
                        $images = explode(',', $restaurant->restaurantImage);
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
                </div>


                <button type="submit" id="updateRestaurant">Update Restaurant</button>

            </form>
        </div>
    </div>
</div>

<script>
    function triggerFileInput(index) {
        document.querySelector("#editModal").querySelector(`input.image-input[data-index="${index}"]`).click();
    }

    function replaceImage(event, index) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.querySelector("#editModal").querySelector(`.preview-image[data-index="${index}"]`).src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    }
</script>
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
        const selectedDays = [...document.querySelector("#editModal").querySelectorAll(".days-dropdown")].map(select => select.value);

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
    document.querySelectorAll(".remove-schedule").forEach(button => {
    button.addEventListener("click", function() {
        button.parentElement.remove();
        updateDaySelection();
    });
});

</script>

<script>
    document.getElementById("openEditModal").addEventListener("click", function() {
        document.getElementById("editModal").style.display = "block";
    });

    document.querySelector(".close").addEventListener("click", function() {
        document.getElementById("editModal").style.display = "none";
    });

    // Close modal if user clicks outside it
    window.onclick = function(event) {
        let modal = document.getElementById("editModal");
        if (event.target == modal) {
            modal.style.display = "none";
        }
    };
</script>
<style>
    #openEditModal,
    #updateRestaurant {
        width: 100%;
        margin-top: 20px;
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

    .modal {
        display: none;
        position: fixed;
        z-index: 100000000000000;
        left: 0;
        top: 0;
        width: 100vw;
        height: 100vh;
        overflow: auto;
        background-color: rgb(0, 0, 0);
        background-color: rgba(0, 0, 0, 0.4);
    }

    .modal-content {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -37.5%);
        background-color: #fff;
        padding: 20px;
        border: 1px solid #888;
        width: 50%;
        border-radius: 10px;
        position: relative;
    }

    .close {
        position: absolute;
        top: 10px;
        right: 15px;
        font-size: 20px;
        cursor: pointer;
    }
</style>
@endsection