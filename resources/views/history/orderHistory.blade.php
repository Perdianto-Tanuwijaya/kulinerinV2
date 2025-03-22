<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction History</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="{{ asset('css/history.css') }}" rel="stylesheet">
</head>

<body>
    @extends('master.masterCustomer')
    @section('content')
        <div class="container py-4">
            <!-- Desktop Filter Section -->
            <div class="row">
                <div class="col-md-3 mb-3 filter-section ms-n2" id="filterSection">
                    <div class="filter-card">
                        <form method="GET" action="{{ route('history') }}" id="desktopFilterForm">
                            <div class="mb-3">
                                <label for="fromDateDesktop" class="form-label">From Date</label>
                                <input type="date" class="form-control" id="fromDateDesktop" name="fromDate"
                                    value="{{ $fromDate ?? '' }}">
                            </div>
                            <div class="mb-3">
                                <label for="toDateDesktop" class="form-label">To Date</label>
                                <input type="date" class="form-control" id="toDateDesktop" name="toDate"
                                    value="{{ $toDate ?? '' }}">
                            </div>
                            <div class="alert alert-danger mt-2" id="dateErrorDesktop" style="display: none;"></div>
                            <button type="submit" class="apply-button w-100" id="applyButtonDesktop">Apply</button>
                        </form>
                    </div>
                </div>


                <!-- Transaction History Section -->
                <div class="col-md-9">
                    <h2 class="mb-4 fw-bold">Transaction History</h2>

                    <hr>
                    @if ($reservations->isEmpty())
                        <p class="text-muted" style="text-align: center; font-style: italic; font-weight: bold;">No
                            reservations found.</p>
                    @else
                        @foreach ($reservations as $reservation)
                            <div class="transaction-card bg-white d-flex mb-3">
                                @php
                                    $imagePaths = explode(',', $reservation->restaurant->restaurantImage);
                                    $firstImage = trim($imagePaths[0]);
                                @endphp
                                <div class="transaction-image">
                                    <div class="image-placeholder h-100">
                                        <img src="{{ asset('storage/' . $firstImage) }}" alt="Restaurant Image"
                                            class="img-fluid image-cover">
                                    </div>
                                </div>
                                <div class="transaction-details p-3">
                                    <h4 class="card-title mb-2">
                                        {{ $reservation->restaurantName ?? ($reservation->restaurantName ?? 'Unknown Restaurant') }}
                                    </h4>
                                    {{-- Kl mau statusnya di kanan atas
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h4 class="card-title mb-2">
                                            {{ $reservation->restaurantName ?? 'Unknown Restaurant' }}
                                        </h4>
                                        <div class="status">
                                            <p class="card-text mb-0">
                                                <span class="status-label badge
                                                    {{ $reservation->reservationStatus == 'CONFIRMED' ? 'bg-success' : 'bg-warning' }}">
                                                    {{ $reservation->reservationStatus }}
                                                </span>
                                            </p>
                                        </div>
                                    </div> --}}
                                    <p class="card-text mb-1">
                                        <span>📍
                                            {{ $reservation->restaurant->restaurantAddress ?? 'Address Not Available' }}
                                        </span>
                                    </p>
                                    <p class="card-text mb-1 fst-italic">
                                        Booking Code : {{ $reservation->bookingCode }}
                                    </p>
                                    <p class="card-text mb-3 fst-italic">
                                        Date:
                                        {{ \Carbon\Carbon::parse($reservation->reservationDate)->locale('en')->translatedFormat('j F Y') }}
                                        |
                                        Time: {{ \Carbon\Carbon::parse($reservation->reservationTime)->format('H:i') }} WIB
                                    </p>
                                    <div class="status">
                                        <p class="card-text">
                                            <span
                                                class="status-label badge
                                                    {{ $reservation->reservationStatus == 'On Going' ? 'bg-warning' : '' }}
                                                    {{ $reservation->reservationStatus == 'Arrived' ? 'bg-primary' : '' }}
                                                    {{ $reservation->reservationStatus == 'Finished' ? 'bg-success' : '' }}
                                                    {{ $reservation->reservationStatus == 'Cancelled' ? 'bg-danger' : '' }}">
                                                {{ $reservation->reservationStatus }}
                                            </span>
                                        </p>
                                        <div class="rating-container mb-3">
                                            @if ($reservation->reservationStatus == 'On Going')
                                                <button class="btn btn-sm btn-outline-danger cancel-order"
                                                    data-id="{{ $reservation->id }}">
                                                    Cancel Order <i class="fas fa-times ms-1"></i>
                                                </button>
                                            @elseif ($reservation->reservationStatus == 'Arrived')
                                                <button class="btn btn-sm btn-outline-primary finish-order"
                                                    data-id="{{ $reservation->id }}">
                                                    Finish Order <i class="fas fa-check ms-1"></i>
                                                </button>
                                            @elseif ($reservation->reservationStatus == 'Finished')
                                                @php
                                                    $userRating = $reservation->restaurant->ratingRestaurants
                                                        ->where('user_id', auth()->id())
                                                        ->where('reservation_id', $reservation->id)
                                                        ->first();
                                                @endphp
                                                @if ($userRating)
                                                    <p class="stars mb-1" style="margin-top: 1rem; font-size: 24px;">
                                                        @for ($i = 0; $i < 5; $i++)
                                                            @if ($i < $userRating->score)
                                                                <span style="color: #f97e0a;">&#9733;</span>
                                                                <!-- Bintang terisi -->
                                                            @else
                                                                <span style="color: gray;">&#9733;</span>
                                                                <!-- Bintang kosong -->
                                                            @endif
                                                        @endfor
                                                    </p>
                                                @else
                                                    <button class="btn btn-sm border-dark give-rating-btn"
                                                        data-reservation-id="{{ $reservation->id }}"
                                                        data-restaurant-id="{{ $reservation->restaurant_id }}"
                                                        data-bs-toggle="modal" data-bs-target="#ratingModal">
                                                        Give Rating <i class="fas fa-edit ms-1"></i>
                                                    </button>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        <p class="text-center mt-4 text-muted">Display all transaction records...</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Overlay Filter Toggle Button -->
        <button class="filter-toggle" id="filterToggle">
            <i class="fas fa-filter me-2"></i>Filters
        </button>

        <!-- Overlay Filter Section -->
        <div class="filter-overlay" id="filterOverlay">
            <div class="filter-content">
                <button class="close-filter" id="closeFilter">×</button>
                <h5 class="mb-3">Filter Transactions</h5>
                <form method="GET" action="{{ route('history') }}" id="mobileFilterForm">
                    <div class="mb-3">
                        <label for="fromDateMobile" class="form-label">From Date</label>
                        <input type="date" class="form-control" id="fromDateMobile" name="fromDate"
                            value="{{ $fromDate ?? '' }}">
                    </div>
                    <div class="mb-3">
                        <label for="toDateMobile" class="form-label">To Date</label>
                        <input type="date" class="form-control" id="toDateMobile" name="toDate"
                            value="{{ $toDate ?? '' }}">
                    </div>
                    <div class="alert alert-danger mt-2" id="dateErrorMobile" style="display: none;"></div>
                    <button type="submit" class="btn apply-button w-100" id="applyButtonMobile">Apply</button>
                </form>
            </div>
        </div>

        <!-- Modal Rating -->
        <div class="modal fade" id="ratingModal" tabindex="-1" aria-labelledby="ratingModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="ratingModalLabel">Give Rating</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="ratingForm">
                            @csrf
                            <input type="hidden" id="restaurant_id" name="restaurant_id">
                            <input type="hidden" id="reservation_id" name="reservation_id">

                            <div class="mb-3">
                                <label for="score" class="form-label">Rating</label>
                                <div id="starRating" class="star-rating d-flex align-items-center mb-2">
                                    <!-- Stars -->
                                    <span class="stars" data-value="1">&#9734;</span>
                                    <span class="stars" data-value="2">&#9734;</span>
                                    <span class="stars" data-value="3">&#9734;</span>
                                    <span class="stars" data-value="4">&#9734;</span>
                                    <span class="stars" data-value="5">&#9734;</span>
                                </div>
                                <input type="hidden" id="score" name="score" value="">
                                <span id="ratingDescription" class=""></span>
                            </div>

                            <div class="mb-3">
                                <label for="review" class="form-label">Review</label>
                                <textarea class="form-control" id="review" name="review" rows="3" required></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary">Submit Rating</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>



        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Handle mobile overlay
                const showFilterOverlay = document.getElementById('showFilterOverlay');
                const closeFilterOverlay = document.getElementById('closeFilterOverlay');
                const filterOverlay = document.getElementById('filterOverlay');

                if (showFilterOverlay) {
                    showFilterOverlay.addEventListener('click', function() {
                        filterOverlay.style.display = 'block';
                        document.body.style.overflow = 'hidden'; // Prevent scrolling behind overlay
                    });
                }

                if (closeFilterOverlay) {
                    closeFilterOverlay.addEventListener('click', function() {
                        filterOverlay.style.display = 'none';
                        document.body.style.overflow = ''; // Re-enable scrolling
                    });
                }

                // Apply validation to both forms
                setupFormValidation('desktopFilterForm', 'fromDateDesktop', 'toDateDesktop', 'dateErrorDesktop',
                    'applyButtonDesktop');
                setupFormValidation('mobileFilterForm', 'fromDateMobile', 'toDateMobile', 'dateErrorMobile',
                    'applyButtonMobile');

                // Sync values between forms (optional)
                syncFormValues('fromDateDesktop', 'fromDateMobile');
                syncFormValues('toDateDesktop', 'toDateMobile');

                function setupFormValidation(formId, fromDateId, toDateId, dateErrorId, applyButtonId) {
                    const form = document.getElementById(formId);
                    const fromDateInput = document.getElementById(fromDateId);
                    const toDateInput = document.getElementById(toDateId);
                    const dateError = document.getElementById(dateErrorId);
                    const applyButton = document.getElementById(applyButtonId);

                    if (!form || !fromDateInput || !toDateInput || !dateError || !applyButton) {
                        return; // Skip if any element doesn't exist
                    }

                    // Form submission handler
                    form.addEventListener('submit', function(event) {
                        // Clear previous error messages
                        dateError.style.display = 'none';
                        fromDateInput.classList.remove('is-invalid');
                        toDateInput.classList.remove('is-invalid');

                        // Check for empty fields
                        const emptyFields = [];
                        if (!fromDateInput.value) {
                            fromDateInput.classList.add('is-invalid');
                            emptyFields.push('From Date');
                        }

                        if (!toDateInput.value) {
                            toDateInput.classList.add('is-invalid');
                            emptyFields.push('To Date');
                        }

                        // Show error for empty fields and prevent submission
                        if (emptyFields.length > 0) {
                            event.preventDefault();
                            dateError.textContent = `Field ${emptyFields.join(' and ')} must be Filled`;
                            dateError.style.display = 'block';
                            return;
                        }

                        // Both dates are filled, perform additional validations
                        const fromDate = new Date(fromDateInput.value);
                        const toDate = new Date(toDateInput.value);

                        // Check if fromDate > toDate
                        if (fromDate > toDate) {
                            event.preventDefault();
                            dateError.textContent = 'From Date must be less than or equal to To Date';
                            dateError.style.display = 'block';
                            return;
                        }

                        // Check if date range exceeds 7 days
                        const timeDiff = toDate - fromDate;
                        const daysDiff = Math.floor(timeDiff / (1000 * 60 * 60 * 24));

                        if (daysDiff > 7) {
                            event.preventDefault();
                            dateError.textContent = 'Date range cannot exceed 7 days';
                            dateError.style.display = 'block';
                            return;
                        }
                    });

                    // Real-time validation
                    function validateInputs() {
                        // Reset errors
                        dateError.style.display = 'none';
                        applyButton.disabled = false;

                        // Skip if either field is empty
                        if (!fromDateInput.value || !toDateInput.value) {
                            return;
                        }

                        const fromDate = new Date(fromDateInput.value);
                        const toDate = new Date(toDateInput.value);

                        // Check date order
                        if (fromDate > toDate) {
                            dateError.textContent = 'From Date must be less than or equal to To Date';
                            dateError.style.display = 'block';
                            applyButton.disabled = true;
                            return;
                        }

                        // Check date range
                        const timeDiff = toDate - fromDate;
                        const daysDiff = Math.floor(timeDiff / (1000 * 60 * 60 * 24));

                        if (daysDiff > 7) {
                            dateError.textContent = 'Date range cannot exceed 7 days';
                            dateError.style.display = 'block';
                            applyButton.disabled = true;
                            return;
                        }
                    }

                    // Add event listeners for real-time validation
                    fromDateInput.addEventListener('change', validateInputs);
                    toDateInput.addEventListener('change', validateInputs);
                }

                // Function to sync values between desktop and mobile forms (optional)
                function syncFormValues(sourceId, targetId) {
                    const sourceInput = document.getElementById(sourceId);
                    const targetInput = document.getElementById(targetId);

                    if (!sourceInput || !targetInput) {
                        return;
                    }

                    sourceInput.addEventListener('change', function() {
                        targetInput.value = sourceInput.value;
                    });

                    targetInput.addEventListener('change', function() {
                        sourceInput.value = targetInput.value;
                    });
                }
            });


            // Toggle filter overlay
            document.getElementById('filterToggle').addEventListener('click', function() {
                document.getElementById('filterOverlay').classList.add('active');
            });

            // Close filter overlay
            document.getElementById('closeFilter').addEventListener('click', function() {
                document.getElementById('filterOverlay').classList.remove('active');
            });

            // Close overlay when clicking outside the filter content
            document.getElementById('filterOverlay').addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.remove('active');
                }
            });

            $(document).ready(function() {
                $(".cancel-order").click(function() {
                    let reservationId = $(this).data("id"); // Ambil ID reservasi dari tombol

                    // Tampilkan SweetAlert untuk konfirmasi
                    Swal.fire({
                        title: "Are you sure?",
                        text: "Do you really want to cancel this reservation?",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Yes, confirm it!"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Jika dikonfirmasi, jalankan AJAX
                            $.ajax({
                                url: `/reservation/${reservationId}/cancel`,
                                type: "DELETE",
                                headers: {
                                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                                        "content") // CSRF Token
                                },
                                success: function(response) {
                                    Swal.fire({
                                        title: "Cancelled!",
                                        text: response.message,
                                        icon: "success",
                                        confirmButtonText: "OK"
                                    }).then(() => {
                                        location
                                            .reload(); // Reload halaman setelah sukses
                                    });
                                },
                                error: function(xhr) {
                                    Swal.fire({
                                        title: "Oopss!",
                                        text: xhr.responseJSON.message ||
                                            "Something went wrong!",
                                        icon: "error",
                                        confirmButtonText: "OK"
                                    });
                                }
                            });
                        }
                    });
                });
            });

            $(document).ready(function() {
                $(".finish-order").click(function() {
                    let reservationId = $(this).data("id"); // Ambil ID reservasi dari tombol

                    // Tampilkan SweetAlert untuk konfirmasi
                    Swal.fire({
                        title: "Are you sure?",
                        text: "Do you really want to finished this reservation?",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Yes, confirm it!"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Jika dikonfirmasi, jalankan AJAX
                            $.ajax({
                                url: `/reservation/${reservationId}/finish`,
                                type: "POST",
                                headers: {
                                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                                        "content") // CSRF Token
                                },
                                success: function(response) {
                                    Swal.fire({
                                        title: "Finished!",
                                        text: response.message,
                                        icon: "success",
                                        confirmButtonText: "OK"
                                    }).then(() => {
                                        location
                                            .reload(); // Reload halaman setelah sukses
                                    });
                                },
                                error: function(xhr) {
                                    Swal.fire({
                                        title: "Error!",
                                        text: xhr.responseJSON.message ||
                                            "Something went wrong!",
                                        icon: "error",
                                        confirmButtonText: "OK"
                                    });
                                }
                            });
                        }
                    });
                });
            });

            document.addEventListener("DOMContentLoaded", function() {
                const stars = document.querySelectorAll("#starRating .stars");
                const ratingDescription = document.getElementById("ratingDescription");

                // Define rating descriptions
                const descriptions = {
                    1: "Very Bad",
                    2: "Bad",
                    3: "Average",
                    4: "Good",
                    5: "Very Good"
                };

                stars.forEach((stars, index) => {
                    stars.addEventListener("click", () => {
                        const rating = index + 1;
                        updateStars(rating);
                        ratingDescription.textContent = descriptions[rating]; // Update description
                        score.value = rating;
                    });
                });

                function updateStars(rating) {
                    stars.forEach((stars, idx) => {
                        if (idx < rating) {
                            stars.classList.add("filled-star");
                            stars.innerHTML = "&#9733;";
                        } else {
                            stars.classList.remove("filled-star");
                            stars.innerHTML = "&#9734;";
                        }
                    });
                }
            });

            $(document).ready(function() {
                $(".give-rating-btn").click(function() {
                    let restaurantId = $(this).data("restaurant-id");
                    let reservationId = $(this).data("reservation-id");

                    console.log(reservationId);
                    $("#restaurant_id").val(restaurantId);
                    $("#reservation_id").val(reservationId);
                });

                $("#ratingForm").on("submit", function(e) {
                    e.preventDefault();

                    let formData = {
                        _token: "{{ csrf_token() }}",
                        restaurant_id: $("#restaurant_id").val(),
                        reservation_id: $("#reservation_id").val(),
                        score: $("#score").val(),
                        review: $("#review").val()
                    };
                    console.log(formData);

                    $.ajax({
                        url: @json(route('rating.store')),
                        method: "POST",
                        data: formData,
                        dataType: "json",
                        success: function(response) {
                            Swal.fire({
                                title: "Success!",
                                text: response.message,
                                icon: "success",
                                confirmButtonText: "OK"
                            }).then(() => {
                                $("#ratingModal").modal("hide");
                                location.reload();

                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                title: "Error!",
                                text: xhr.responseJSON.message || "Something went wrong!",
                                icon: "error",
                                confirmButtonText: "OK"
                            });
                        }
                    });
                });
            });
        </script>
    @endsection
</body>

</html>
