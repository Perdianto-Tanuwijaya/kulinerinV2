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
use App\Models\User;
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

        return view('index.restaurantSearchIndex', compact('restaurants'));
    }

    public function indexRestaurants($id)
    {
        $restaurants = Restaurant::findOrFail($id);
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

        return view('index.restaurantIndex', compact('restaurants', 'ratingData', 'images', 'menuItems', 'capacities', 'totalAvailableTables', 'operationalHour'));
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


    // public function getAvailableTables(Request $request)
    // {
    //     $restaurantId = $request->input('restaurant_id'); // Ambil restaurant_id dari request
    //     $reservationDate = $request->input('date'); // Ambil tanggal dari request

    //     // Validasi input (optional)
    //     if (!$restaurantId || !$reservationDate) {
    //         return response()->json(['error' => 'Missing restaurant_id or date'], 400);
    //     }

    //     // Ambil hanya meja yang sesuai dengan restaurant_id
    //     $tables = TableRestaurant::where('restaurant_id', $restaurantId)->get()->map(function ($table) use ($reservationDate) {
    //         // Hitung jumlah reservasi untuk meja ini pada tanggal tertentu
    //         $reservedCount = Reservation::where('table_restaurant_id', $table->id)
    //             ->where('reservationDate', $reservationDate)
    //             ->count();

    //         // Hitung sisa meja yang tersedia
    //         $availableTables = max(0, $table->availableTables - $reservedCount);

    //         return [
    //             'id' => $table->id,
    //             'capacity' => $table->tableCapacity,
    //             'available' => $availableTables,
    //         ];
    //     });

    //     return response()->json($tables);
    // }




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
    public function indexMenu($id)
    {
        $menuItems = MenuRestaurant::where('restaurant_id', $id)
            ->orderBy('category')
            ->get()
            ->groupBy('category');
        $restaurants = Restaurant::find($id);

        return view('menu.index', compact('menuItems', 'restaurants'));
    }

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

        $restaurant->restaurantImage = implode(',', $existingImages);
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
