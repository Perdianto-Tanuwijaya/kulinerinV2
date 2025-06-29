<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Restaurant;
use App\Models\MenuRestaurant;
use App\Models\RatingRestaurant;
use App\Models\Reservation;
use App\Models\TableRestaurant;
use App\Models\OperationalHour;
use Illuminate\Support\Str;
// use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;



class RestaurantController extends Controller
{
    public function searchRestaurant(Request $request)
    {
        $search = $request->query('keyword');
        $location = $request->query('location');
        $minRating = $request->input('min_rating');
        $filterDays = $request->input('opening_day', []);
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

        if ($location) {
            $locationWords = explode(' ', $location);
            $restaurants = $restaurants->where(function ($query) use ($locationWords) {
                foreach ($locationWords as $word) {
                    $query->where(function ($q) use ($word) {
                        $q->where('restaurantAddress', 'like', '%' . $word . '%');
                    });
                }
            });
        }


        // Filter hari buka
        if (!empty($filterDays)) {
            $restaurants = $restaurants->whereHas('operationalHours', function ($query) use ($filterDays) {
                $query->whereIn('day', $filterDays)
                    ->whereNotNull('open_time')
                    ->whereNotNull('close_time');
            }, '=', count($filterDays)); // Pastikan restoran buka di semua hari yang dipilih
        }

        // Ambil restoran beserta operational_hours (kalau mau tampilkan jamnya juga)
        $restaurants = $restaurants->with(['operationalHours' => function ($query) use ($filterDays) {
            if (!empty($filterDays)) {
                $query->whereIn('day', $filterDays)
                    ->whereNotNull('open_time')
                    ->whereNotNull('close_time');
            }
        }])->paginate(5);

        // Proses transformasi hasil
        $restaurants->getCollection()->transform(function ($restaurant) use ($minRating) {
            $ratingData = $this->getRating($restaurant->id); // call rating function
            $restaurant->restaurantImage = strtok($restaurant->restaurantImage, ',');
            $restaurant->averageScore = $ratingData['averageScore'];
            $restaurant->totalReviewers = $ratingData['totalReviewers'];

            return $restaurant;
        });

        // Filter berdasarkan minimal rating
        // if ($minRating) {
        //     $restaurants->setCollection($restaurants->getCollection()->filter(function ($restaurant) use ($minRating) {
        //         $minRange = $minRating;
        //         $maxRange = $minRating + 0.99; // Range rating 4.0 - 4.99 misal
        //         return $restaurant->averageScore >= $minRange && $restaurant->averageScore < $maxRange;
        //     }));
        // }
        if ($minRating) {
            $restaurants->setCollection($restaurants->getCollection()->filter(function ($restaurant) use ($minRating) {
                // Tentukan minimum dan maksimum rating berdasarkan pilihan pengguna
                $minRange = $minRating;  // minRating (dari input pengguna)
                $maxRange = 5;  // Batas atas selalu 5 karena rating tertinggi adalah 5

                // Filter restoran berdasarkan rentang rating yang dipilih
                return $restaurant->averageScore >= $minRange && $restaurant->averageScore <= $maxRange;
            }));
        }


        return view('index.restaurantSearchIndex', compact('restaurants'));
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

        return view('index.restaurantIndex', compact('restaurants', 'openingHours', 'ratingData', 'images', 'menuItems', 'capacities', 'totalAvailableTables', 'operationalHour'));
    }

    public function checkAvailableTables(Request $request)
    {
        // Validasi input
        $request->validate([
            'guest' => 'required|integer',
            'reservationDate' => 'required|date',
            'reservationTime' => 'required',
            'restaurant_id' => 'required|integer'
        ]);

        $guest = $request->guest;
        $date = $request->reservationDate;
        $time = $request->reservationTime;
        $restaurantId = $request->restaurant_id;

        // Cari meja yang sesuai dengan kapasitas dan restaurant_id
        $tables = TableRestaurant::where('restaurant_id', $restaurantId)
            ->where('tableCapacity', '>=', $guest)
            ->orderBy('tableCapacity', 'asc')
            ->get();

        if ($tables->isEmpty()) {
            return response()->json(['availableTables' => 0]);
        }

        // Ubah format waktu untuk pencarian
        $reservationDateTime = Carbon::parse($date . ' ' . $time);
        $availableTables = 0;

        foreach ($tables as $table) {
            // Tentukan durasi reservasi (2 jam)
            $reservationStart = $reservationDateTime->copy();
            $reservationEnd = $reservationDateTime->copy()->addHours(2);

            // Cek reservasi yang tumpang tindih
            $existingReservations = Reservation::where('table_restaurant_id', $table->id)
                ->where(function ($query) use ($reservationStart, $reservationEnd) {
                    $query->whereRaw("CONCAT(reservationDate, ' ', reservationTime) <= ?", [$reservationEnd->format('Y-m-d H:i')])
                        ->whereRaw("DATE_ADD(CONCAT(reservationDate, ' ', reservationTime), INTERVAL 2 HOUR) >= ?", [$reservationStart->format('Y-m-d H:i')])
                        // ->whereIn('reservationStatus', 'On Going', 'Arrived');
                        ->whereIn('reservationStatus', ['On Going', 'Arrived']);
                })
                ->count();

            // Hitung jumlah meja yang tersedia
            $availableTables += max(0, $table->availableTables - $existingReservations);
        }

        return response()->json(['availableTables' => $availableTables]);
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
    // public function indexMenu($id)
    // {
    //     $menuItems = MenuRestaurant::where('restaurant_id', $id)
    //         ->orderBy('category')
    //         ->get()
    //         ->groupBy('category');
    //     $restaurants = Restaurant::find($id);

    //     return view('menu.index', compact('menuItems', 'restaurants'));
    // }

    public function settings()
    {
        $user = Auth::user();
        $restaurant = Restaurant::where('user_id', $user->id)->first();

        $operationalHours = DB::table('operational_hours')
            ->where('restaurant_id', $restaurant->id)
            ->get();

        // Define the correct order of days
        $dayOrder = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];

        $schedules = $operationalHours->map(function ($row) {
            return [
                'day' => $row->day,
                'open_time' => $row->open_time,
                'close_time' => $row->close_time
            ];
        })->sortBy(function ($schedule) use ($dayOrder) {
            return array_search($schedule['day'], $dayOrder);
        })->values()->toArray(); // Reindex the array after sorting

        return view('restaurant.settings.index', compact('restaurant', 'schedules'));
    }



    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'number' => 'required|string|max:20',
            'city' => 'required|string|max:100',
            'address' => 'required|string|max:255',
            'desc' => 'required|string',
            'style' => 'required|string|in:Asian,Western,Fine Dining,Bar',
            'image' => 'nullable|array',
            'image.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'days' => 'required|array',
            'open_time' => 'required|array',
            'close_time' => 'required|array',
        ]);

        $user = auth()->user();
        $restaurant = Restaurant::findOrFail($id);
        $restaurantNameSlug = Str::slug($request->name);

        // Update restaurant details
        $restaurant->restaurantName = $request->name;
        $restaurant->restaurantPhoneNumber = $request->number;
        $restaurant->restaurantCity = $request->city;
        $restaurant->restaurantAddress = $request->address;
        $restaurant->restaurantDescription = $request->desc;
        $restaurant->restaurantStyle = $request->style;

        // Handle images
        $existingImages = $request->input('existing_images', []);
        $existingImages = array_map(fn($img) => $img === 'null' ? null : $img, $existingImages);

        if ($request->hasFile('image')) {
            foreach ($request->file('image') as $index => $image) {
                $extension = $image->getClientOriginalExtension();
                $filename = "{$user->id}-{$restaurantNameSlug}" . ($index === 0 ? '' : $index) . ".{$extension}";
                $image->move(public_path('storage/restaurant'), $filename);
                $existingImages[$index] = "restaurant/" . $filename;
            }
        }

        // Convert array to a comma-separated string for database storage
        $restaurant->restaurantImage = implode(', ', $existingImages);

        $restaurant->save();

        // Delete existing operational hours
        DB::table('operational_hours')->where('restaurant_id', $restaurant->id)->delete();

        // Insert new operational hours
        $days = $request->days;
        $openTimes = $request->open_time;
        $closeTimes = $request->close_time;

        $newSchedules = [];
        foreach ($days as $index => $day) {
            $newSchedules[] = [
                'restaurant_id' => $restaurant->id,
                'day' => $day,
                'open_time' => $openTimes[$index],
                'close_time' => $closeTimes[$index],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('operational_hours')->insert($newSchedules);

        return redirect()->back()->with('success', 'Restaurant details and schedule updated successfully!');
    }
}
