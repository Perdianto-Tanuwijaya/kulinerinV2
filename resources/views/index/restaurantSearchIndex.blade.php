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
            margin-bottom: 1.5rem;
        }

        .filter-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #333;
        }

        .filter-input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 0.9rem;
            color: #333;
            background: #f8f8f8;
        }

        .filter-input:focus {
            outline: none;
            border-color: #999;
            background: white;
        }

        .rating-options {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .rating-option {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .operational-hours {
            display: flex;
            gap: 0.5rem;
        }

        .time-input {
            width: 50%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 0.9rem;
        }

        .apply-btn {
            width: 100%;
            padding: 0.75rem;
            background-color: #D67B47ff;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
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
    @extends('master.masterCustomer')
    @section('content')
        <main class="main-content">
            <aside class="filters">
                <form action="{{ route('searchRestaurant') }}" method="GET">
                    <input type="hidden" name="keyword" value="{{ request('keyword') }}">
                    <input type="hidden" name="location" value="{{ request('location') }}">
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

                    {{-- <div class="filter-section">
                        <h3 class="filter-title">Operational Hour</h3>
                        <div class="operational-hours">
                            <input type="time" class="time-input" name="open_time" value="{{ request('open_time') }}">
                            <input type="time" class="time-input" name="close_time" value="{{ request('close_time') }}">
                        </div>
                    </div> --}}

                    <button type="submit" class="apply-btn">Apply</button>
                </form>
            </aside>
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
</body>

</html>
