<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\Advertisement;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function customerDashboard()
    {
        $restaurants = Restaurant::inRandomOrder()->take(3)->get();
        // $restaurants = Restaurant::where('restaurantStyle', 'Casual Dining, Indonesian')
        //                  ->inRandomOrder()
        //                  ->take(3)
        //                  ->get();
        $restaurantsDine = Restaurant::inRandomOrder()->take(3)->get();
        $restaurantsHoliday = Restaurant::inRandomOrder()->take(3)->get();

        $advertisements = Advertisement::all(); // Ambil semua data dari database

        // dd($advertisements);
        $advertisements->transform(function ($ad) {
            $ad->images = explode(', ', $ad->adImage);
            return $ad;
        });
        // dd($advertisements);

        // foreach ($restaurants as $restaurant) {
        //     $restaurant->firstImage = explode(', ', $restaurant->restaurantImage)[0];
        // }

        // foreach ($restaurantsDine as $restaurant) {
        //     $restaurant->firstImage = explode(', ', $restaurant->restaurantImage)[0];
        // }

        // foreach ($restaurantsHoliday as $restaurant) {
        //     $restaurant->firstImage = explode(', ', $restaurant->restaurantImage)[0];
        // }

        foreach ($restaurants as $restaurant) {
            $restaurant->firstImage = $this->getRandomImage($restaurant->restaurantImage);
        }

        foreach ($restaurantsDine as $restaurant) {
            $restaurant->firstImage = $this->getRandomImage($restaurant->restaurantImage);
        }

        foreach ($restaurantsHoliday as $restaurant) {
            $restaurant->firstImage = $this->getRandomImage($restaurant->restaurantImage);
        }
        return view('dashboard.customerDashboard', compact('restaurants', 'restaurantsDine', 'restaurantsHoliday', 'advertisements'));
    }

    public function getRandomImage($restaurantImage)
    {
        if ($restaurantImage) {
            $images = explode(', ', $restaurantImage);
            return $images[array_rand($images)];
        }
        return null;
    }
}
