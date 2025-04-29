<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="icon" href="{{ asset('asset/kulinerinLogo.png') }}" type="image/png">
    <title>
        KULINERIN | Search Restaurant
    </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <meta charset="UTF-8">
    <link href="{{ asset('css/searchIndex.css') }}" rel="stylesheet">
</head>

<body>
    @extends('master.masterCustomer')
    @section('content')
        <main class="main-content">
            <!-- Filter Section for Desktop -->
            <div class="col-md-3 mb-3 filter-section ms-n2" id="filterSection">
                <aside class="filters">
                    <form action="{{ route('searchRestaurant') }}" method="GET">
                        <input type="hidden" name="keyword" value="{{ request('keyword') }}">
                        <input type="hidden" name="location" value="{{ request('location') }}">
                        <div class="filter-section">
                            <h3 class="filter-title">Rating</h3>
                            @php
                                $stars = [
                                    1 => '★☆☆☆☆',
                                    2 => '★★☆☆☆',
                                    3 => '★★★☆☆',
                                    4 => '★★★★☆',
                                    5 => '★★★★★',
                                ];
                            @endphp
                            <div class="rating-options">
                                @foreach ($stars as $value => $star)
                                    <label class="rating-option">
                                        <input type="radio" name="min_rating" value="{{ $value }}"
                                            {{ request('min_rating') == $value ? 'checked' : '' }}>
                                        <span class="stars">{{ $star }} ({{ $value }}
                                            Star{{ $value > 1 ? 's' : '' }} {{ $value == 5 ? 'Only' : '& Above' }})</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="filter-section">
                            <h3 class="filter-title">Opening Day</h3>
                            <div class="rating-options">
                                <label class="day-option">
                                    <input type="checkbox" name="opening_day[]" value="Monday"
                                        {{ is_array(request('opening_day')) && in_array('Monday', request('opening_day')) ? 'checked' : '' }}>
                                    <span class="day-name">Monday</span>
                                </label>
                                <label class="day-option">
                                    <input type="checkbox" name="opening_day[]" value="Tuesday"
                                        {{ is_array(request('opening_day')) && in_array('Tuesday', request('opening_day')) ? 'checked' : '' }}>
                                    <span class="day-name">Tuesday</span>
                                </label>
                                <label class="day-option">
                                    <input type="checkbox" name="opening_day[]" value="Wednesday"
                                        {{ is_array(request('opening_day')) && in_array('Wednesday', request('opening_day')) ? 'checked' : '' }}>
                                    <span class="day-name">Wednesday</span>
                                </label>
                                <label class="day-option">
                                    <input type="checkbox" name="opening_day[]" value="Thursday"
                                        {{ is_array(request('opening_day')) && in_array('Thursday', request('opening_day')) ? 'checked' : '' }}>
                                    <span class="day-name">Thursday</span>
                                </label>
                                <label class="day-option">
                                    <input type="checkbox" name="opening_day[]" value="Friday"
                                        {{ is_array(request('opening_day')) && in_array('Friday', request('opening_day')) ? 'checked' : '' }}>
                                    <span class="day-name">Friday</span>
                                </label>
                                <label class="day-option">
                                    <input type="checkbox" name="opening_day[]" value="Saturday"
                                        {{ is_array(request('opening_day')) && in_array('Saturday', request('opening_day')) ? 'checked' : '' }}>
                                    <span class="day-name">Saturday</span>
                                </label>
                                <label class="day-option">
                                    <input type="checkbox" name="opening_day[]" value="Sunday"
                                        {{ is_array(request('opening_day')) && in_array('Sunday', request('opening_day')) ? 'checked' : '' }}>
                                    <span class="day-name">Sunday</span>
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="apply-btn">Apply</button>
                    </form>
                </aside>
            </div>

            <!-- Overlay Filter Toggle Button -->
            <button class="filter-toggle" id="filterToggle">
                <i class="fas fa-filter me-2"></i>Filters
            </button>

            <!-- Overlay Filter Section -->
            <div class="filter-overlay" id="filterOverlay">
                <div class="filter-content">
                    <button class="close-filter" id="closeFilter">×</button>
                    <h5 class="mb-3">Filter Restaurants</h5>
                    <form action="{{ route('searchRestaurant') }}" method="GET">
                        <input type="hidden" name="keyword" value="{{ request('keyword') }}">
                        <input type="hidden" name="location" value="{{ request('location') }}">
                        {{-- <div class="filter-section"> --}}
                        {{-- <h3 class="filter-title">Rating</h3> --}}
                        <div class="rating-options">
                            <label class="rating-option">
                                <input type="radio" name="min_rating" value="5"
                                    {{ request('min_rating') == 5 ? 'checked' : '' }}>
                                <span class="stars">★★★★★</span>
                            </label>
                            <label class="rating-option">
                                <input type="radio" name="min_rating" value="4"
                                    {{ request('min_rating') == 4 ? 'checked' : '' }}>
                                <span class="stars">★★★★☆</span>
                            </label>
                            <label class="rating-option">
                                <input type="radio" name="min_rating" value="3"
                                    {{ request('min_rating') == 3 ? 'checked' : '' }}>
                                <span class="stars">★★★☆☆</span>
                            </label>
                            <label class="rating-option">
                                <input type="radio" name="min_rating" value="2"
                                    {{ request('min_rating') == 2 ? 'checked' : '' }}>
                                <span class="stars">★★☆☆☆</span>
                            </label>
                            <label class="rating-option">
                                <input type="radio" name="min_rating" value="1"
                                    {{ request('min_rating') == 1 ? 'checked' : '' }}>
                                <span class="stars">★☆☆☆☆</span>
                            </label>
                        </div>
                        {{-- </div> --}}

                        {{-- <div class="filter-section">
                            <h3 class="filter-title">Operational Hour</h3>
                            <div class="operational-hours">
                                <input type="time" class="time-input" name="open_time" value="{{ request('open_time') }}">
                                <input type="time" class="time-input" name="close_time" value="{{ request('close_time') }}">
                            </div>
                        </div> --}}

                        <button type="submit" class="apply-btn mt-3">Apply</button>
                    </form>
                </div>
            </div>

            <div class="results">
                @if ($restaurants->isEmpty())
                    <h3 class="not-found-text">Restaurant Not Found</h3>
                @else
                    @foreach ($restaurants as $restaurant)
                        <div class="restaurant-card-search"
                            onclick="window.location='{{ route('indexRestaurants', $restaurant->id) }}'">
                            <div class="restaurant-image-search">
                                <img src="{{ asset('storage/' . $restaurant->restaurantImage) }}" alt="Restaurant Image">
                            </div>
                            <div class="restaurant-info">
                                <h2 class="restaurant-name">{{ $restaurant->restaurantName }}</h2>
                                <div class="restaurant-address line-break">
                                    <span>
                                        📍
                                    </span>
                                    {{ $restaurant->restaurantAddress }}
                                </div>
                                @php
                                    $fullStars = floor($restaurant->averageScore); // Full stars
                                    $halfStar = $restaurant->averageScore - $fullStars >= 0.5; // Check for half-star
                                    $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0); // Remaining empty stars
                                @endphp
                                <div class="rating">
                                    {{-- <span class="stars">★★★★☆</span>
                        <span>(100 Reviews)</span> --}}
                                    {{-- Display full stars --}}
                                    <span class="stars">
                                        @for ($i = 0; $i < $fullStars; $i++)
                                            ★
                                        @endfor

                                        {{-- Display half star if applicable --}}
                                        @if ($halfStar)
                                            ⯪
                                        @endif

                                        {{-- Display empty stars --}}
                                        @for ($i = 0; $i < $emptyStars; $i++)
                                            ☆
                                        @endfor
                                    </span>

                                    {{-- Show total reviewers --}}
                                    <span>({{ $restaurant->totalReviewers }} Reviews)</span>

                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </main>
    @endsection


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle filter overlay
            document.getElementById('filterToggle').addEventListener('click', function() {
                document.getElementById('filterOverlay').classList.add('active');
                document.body.style.overflow = 'hidden'; // Prevent background scrolling
            });

            // Close filter overlay
            document.getElementById('closeFilter').addEventListener('click', function() {
                document.getElementById('filterOverlay').classList.remove('active');
                document.body.style.overflow = ''; // Enable background scrolling
            });

            // Close overlay when clicking outside the filter content
            document.getElementById('filterOverlay').addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.remove('active');
                    document.body.style.overflow = ''; // Enable background scrolling
                }
            });
        });
    </script>
</body>

</html>
