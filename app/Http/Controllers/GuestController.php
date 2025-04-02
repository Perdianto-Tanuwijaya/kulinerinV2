<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\Advertisement;
use App\Models\MenuRestaurant;
use App\Models\OperationalHour;
use App\Models\TableRestaurant;
use Illuminate\Http\Request;

class GuestController extends Controller
{
    public function guestDashboard()
    {
        $restaurants = Restaurant::inRandomOrder()->take(3)->get();
        $restaurantsDine = Restaurant::inRandomOrder()->take(3)->get();
        $restaurantsHoliday = Restaurant::inRandomOrder()->take(3)->get();

        $advertisements = Advertisement::all(); // Ambil semua data dari database

        $advertisements->transform(function ($ad) {
            $ad->images = explode(', ', $ad->adImage);
            return $ad;
        });

        foreach ($restaurants as $restaurant) {
            $restaurant->firstImage = $this->getRandomImage($restaurant->restaurantImage);
        }

        foreach ($restaurantsDine as $restaurant) {
            $restaurant->firstImage = $this->getRandomImage($restaurant->restaurantImage);
        }

        foreach ($restaurantsHoliday as $restaurant) {
            $restaurant->firstImage = $this->getRandomImage($restaurant->restaurantImage);
        }
        return view('dashboard.guestDashboard', compact('restaurants', 'restaurantsDine', 'restaurantsHoliday', 'advertisements'));
    }
    public function getRandomImage($restaurantImage)
    {
        if ($restaurantImage) {
            $images = explode(', ', $restaurantImage);
            return $images[array_rand($images)];
        }
        return null;
    }

    public function searchRestaurantbyGuest(Request $request)
    {
        $search = $request->query('keyword');
        $minRating = $request->query('min_rating');
        $restaurants = Restaurant::query();

        if ($search) {
            $searchWords = explode(' ', $search);
            $restaurants = $restaurants->where(function ($query) use ($searchWords) {
                foreach ($searchWords as $word) {
                    $query->where(function ($q) use ($word) {
                        $q->where('restaurantName', 'like', '%' . $word . '%');
                        //   ->orWhere('restaurantAddress', 'like', '%' . $word . '%');
                    });
                }
            });
        }

        $restaurants = $restaurants->paginate(5);
        $restaurants->getCollection()->transform(function ($restaurant) use ($minRating) {
            $ratingData = $this->getRating($restaurant->id); // call rating function
            $restaurant->restaurantImage = strtok($restaurant->restaurantImage, ',');
            $restaurant->averageScore = $ratingData['averageScore'];
            $restaurant->totalReviewers = $ratingData['totalReviewers'];

            return $restaurant;
        });

        if ($minRating) {
            $restaurants->setCollection($restaurants->getCollection()->filter(function ($restaurant) use ($minRating) {
                // Calculate the minimum and maximum based on the selected rating
                $minRange = $minRating;
                $maxRange = $minRating + 0.99; // The upper bound is one less than the next whole number

                // Filter restaurants based on the dynamic range
                return $restaurant->averageScore >= $minRange && $restaurant->averageScore < $maxRange;
            }));
        }

        return view('index.restaurantSearchIndexGuest', compact('restaurants'));
    }

    public function indexRestaurants($id)
    {
        $restaurants = Restaurant::findOrFail($id);
        $openingHours = OperationalHour::where('restaurant_id', $restaurants->id)->get();
        $menuItems = MenuRestaurant::where('restaurant_id', $id)
            ->orderBy('category')
            ->get()
            ->groupBy('category');
        $images = explode(', ', $restaurants->restaurantImage);

        $operationalHour = Restaurant::with('operationalHours')->find($id);

        $ratingData = $this->getRating($id);

        // Ambil kapasitas tertinggi dari meja yang tersedia
        $maxCapacity = TableRestaurant::where('restaurant_id', $id)
            ->max('tableCapacity');
        // dd($maxCapacity);
        // Pastikan $maxCapacity adalah integer, jika null set default ke 1
        $maxCapacity = $maxCapacity ? (int) $maxCapacity : 1;

        // Buat array dari 1 sampai kapasitas tertinggi
        $capacities = range(1, $maxCapacity);

        $totalAvailableTables = TableRestaurant::where('restaurant_id', $id)
            ->sum('availableTables');

        return view('index.restaurantIndexGuest', compact('restaurants', 'openingHours', 'ratingData', 'images', 'menuItems', 'capacities', 'totalAvailableTables', 'operationalHour'));
    }

    public function getRating($id)
    {
        $restaurant = Restaurant::with('ratingRestaurants.user')->findOrFail($id);

        return [
            'restaurant' => $restaurant,
            'averageScore' => $restaurant->ratingRestaurants->avg('score') ?? 0,
            'totalReviewers' => $restaurant->ratingRestaurants->count(),
            'reviews' => $restaurant->ratingRestaurants,
        ];
    }
}
