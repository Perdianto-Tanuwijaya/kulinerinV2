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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: system-ui, -apple-system, sans-serif;
        }

        .main-content {
            display: flex;
            padding: 2rem;
            gap: 2rem;
            background-color: #DECEB0ff;
            min-height: 100vh;
        }

        .filters {
            width: 300px;
            background: #F0D4A3;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            height: fit-content;
        }

        .filter-section {
            margin-bottom: 20px;
        }

        .filter-title {
            font-size: 16px;
            margin-bottom: 10px;
            color: #333;
        }

        .rating-options {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .rating-option {
            cursor: pointer;
            display: flex;
            align-items: center;
        }

        .stars {
            color: #ffc107;
            margin-left: 8px;
        }

        .apply-btn {
            background-color: #D67B47;
            border: none;
            border-radius: 4px;
            color: white;
            cursor: pointer;
            font-size: 14px;
            padding: 10px 15px;
            text-align: center;
            text-decoration: none;
            width: 100%;
        }

        .apply-btn:hover {
            background-color: #c26a3a;
        }

        /* Overlay filter button */
        .filter-toggle {
            display: none;
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: auto;
            z-index: 1000;
            border-radius: 50px;
            padding: 10px 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            background-color: #D67B47;
            color: white;
            border: none;
            font-weight: 500;
        }

        /* Positioning for filter section when shown as overlay */
        .filter-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
            display: none;
            justify-content: center;
            align-items: center;
        }

        .filter-content {
            width: 90%;
            max-width: 350px;
            max-height: 90vh;
            overflow-y: auto;
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            position: relative;
        }

        .close-filter {
            position: absolute;
            top: 10px;
            right: 10px;
            background: transparent;
            border: none;
            font-size: 1.5rem;
            line-height: 1;
        }

        /* Responsive styles */
        @media (max-width: 767.98px) {
            .filter-section {
                display: none;
            }

            .filter-toggle {
                display: block;
            }

            .filter-overlay.active {
                display: flex;
            }
        }


        .results {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .restaurant-card-search {
            display: flex;
            cursor: pointer;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .restaurant-image-search {
            width: 200px;
            height: 150px;
            background-color: #eee;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #666;
        }

        .restaurant-info {
            padding: 1.5rem;
            flex-grow: 1;
        }

        .restaurant-name {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .restaurant-address {
            color: #666;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .rating {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .stars {
            color: #f97e0a;
        }

        .not-found-text {
            font-size: 20px;
            color: rgb(79, 75, 72);
            font-family: 'Poppins', sans-serif;
            display: flex;
            justify-content: center;
            text-align: center;

        }

        .line-break {
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 1;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
</head>

<body>
    @extends('master.masterGuest')
    @section('content')
        <main class="main-content">
            <!-- Filter Section for Desktop -->
            <div class="col-md-3 mb-3 filter-section ms-n2" id="filterSection">
                <aside class="filters">
                    <form action="{{ route('search') }}" method="GET">
                        <input type="hidden" name="keyword" value="{{ request('keyword') }}">
                        <div class="filter-section">
                            <h3 class="filter-title">Rating</h3>
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
                    <form action="{{ route('search') }}" method="GET">
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
                            onclick="window.location='{{ route('indexRestaurantsGuest', $restaurant->id) }}'">
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
