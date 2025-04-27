<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Mail\ReceiptMail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use Illuminate\Support\Facades\Redirect;
// use App\Models\Receipt;
use App\Models\Reservation;
use App\Models\Restaurant;
use App\Models\TableRestaurant;
use App\Models\PointLoyalty;
// use App\Models\Payment;
use App\Models\RestaurantBalance;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    public function booking(Request $request)
    {
        $request->validate([
            'restaurantId' => 'required|exists:restaurants,id',
            'guest' => 'required|integer|min:1',
            'reservationDate' => 'required|date',
            'reservationTime' => 'required',
        ]);

        $reservationDateTime = Carbon::parse($request->reservationDate . ' ' . $request->reservationTime);

        // Cari meja yang memiliki kapasitas cukup di restoran yang dipilih
        $tables = TableRestaurant::where('restaurant_id', $request->restaurantId)
            ->where('tableCapacity', '>=', $request->guest)
            ->where('availableTables', '>', 0)
            ->orderBy('tableCapacity', 'asc')
            ->get();

        if ($tables->isEmpty()) {
            return redirect()->back()->with('error', "There's no available table.");
        }

        $availableTable = null;

        foreach ($tables as $table) {
            // Tentukan durasi reservasi (misalnya 2 jam)
            $reservationStart = $reservationDateTime->copy();
            $reservationEnd = $reservationDateTime->copy()->addHours(2);

            // Cek apakah ada reservasi yang bertabrakan dengan waktu yang dipilih
            $overlappingReservations = Reservation::where('table_restaurant_id', $table->id)
                ->where(function ($query) use ($reservationStart, $reservationEnd) {
                    $query->whereRaw("CONCAT(reservationDate, ' ', reservationTime) <= ?", [$reservationEnd->format('Y-m-d H:i')])
                        ->whereRaw("DATE_ADD(CONCAT(reservationDate, ' ', reservationTime), INTERVAL 2 HOUR) >= ?", [$reservationStart->format('Y-m-d H:i')]);
                })
                ->count();

            if ($overlappingReservations < $table->availableTables) {
                $availableTable = $table;
                break;
            }
        }

        if (!$availableTable) {
            return redirect()->back()->with('error', "There's no available tables for the selected date and time.");
        }

        $booking = new Reservation();
        $booking->user_id = auth()->id();
        $booking->guest = $request->guest;
        // $booking->tableType = $request->tableType;
        $booking->reservationDate = $request->reservationDate;
        $booking->reservationTime = $request->reservationTime;
        $booking->restaurantName = $request->restaurantName;
        $booking->reservationStatus = 'On Going';
        $booking->bookingCode = 'NER' . strtoupper(bin2hex(random_bytes(4))) . 'IN';
        $booking->priceTotal = $request->priceTotal;
        $booking->restaurant_id = $request->restaurantId;
        $booking->table_restaurant_id = $table->id;

        // Cek apakah menuData dikirimkan
        if (!empty($request->menuData)) {
            // DECODE MENU DATA KRNA DRI DEPAN DIKIRIMNYA DATA ENCODE
            $decodedMenuData = html_entity_decode($request->menuData);
            $menuArray = json_decode($decodedMenuData, true);

            // CEK VALID ATAU GA JSON YG DIKIRIM DRI DEPAN
            if (json_last_error() === JSON_ERROR_NONE && is_array($menuArray)) {
                // KL VALID, MASUK KE SINI BUAT DI AMBIL VALUE ARRAY
                $menuStrings = [];
                foreach ($menuArray as $menu) {
                    $menuStrings[] = "{$menu['qty']}x {$menu['menuName']} - Rp " . number_format($menu['menuPrice'], 0, ',', '');
                    // HASILNYA
                    // [
                    //     "1x Spring Rolls - Rp 10,000",
                    //     "1x Garlic Bread - Rp 45,000"
                    // ]
                }
                // DISINI HASIL ARRAYNYA DI JADIIN 1 LINE STRING
                // "1x Spring Rolls - Rp 10,000, 1x Garlic Bread - Rp 45,000"
                $booking->menuData = implode(', ', $menuStrings);
            } else {
                $booking->menuData = null;
            }
        } else {
            //MASUK SINI KL CM BOOK MEJA
            $booking->menuData = null; //
        }

        $booking->save();

        // Data untuk email notifikasi
        $reservationData = [
            'id' => $booking->id,
            'bookingCode' => $booking->bookingCode,
            'reservationDate' => $booking->reservationDate,
            'reservationTime' => $booking->reservationTime,
            'guest' => $request->guest,
            'menuDetails' => $booking->menuData, // Bisa null jika tidak ada menu
            'username' => auth()->user()->username,
        ];

        // Kirim ke email (user) yang terdaftar pada apps
        if (auth()->check()) {
            Mail::to(auth()->user()->email)->send(new ReceiptMail($reservationData));
        } else {
            if ($request->isJson()) {
                return response()->json(['error' => 'Reservation Failed']);
            } else {
                return redirect("/dashboardCustomer")->with('error', 'Reservation Failed');
            }
        }

        // Return response sesuai dengan format request
        if ($request->isJson()) {
            return response()->json([
                'code' => $booking->bookingCode,
                'menuDetails' => $booking->menuData ?? 'No menu items were ordered.', // Nampilin Gada menu yang diorder kl menu datanya NULL
            ]);
        } else {
            return redirect("/dashboardCustomer")->with('success', 'Reservation Success! Booking Code: ' . $booking->bookingCode);
        }
    }

    public function detailOrder(Request $request)
    {
        $orderData = json_decode($request->input('orderData'), true);
        $orderDataJson = $request->input('orderData');
        $grandTotal = $request->grandTotal;
        $restaurantId = $request->restaurantId;
        $guestInfo = $request->guestInfo; // Guest info
        $areaInfo = $request->areaInfo;
        $reservationDate = $request->reservationDate;
        $reservationTime = $request->reservationTime;
        $restaurantName = $request->restaurantName; // Restaurant name
        $restaurantCity = $request->restaurantCity; // Restaurant city
        // $restaurants = Restaurant::find($request);
        // dd($grandTotal);
        return view('order.orderDetail', compact('orderData', 'orderDataJson', 'grandTotal', 'guestInfo', 'areaInfo', 'reservationDate', 'restaurantId', 'reservationTime', 'restaurantName', 'restaurantCity'));
    }

    public function history(Request $request)
    {
        // Ambil user yang sedang login
        $user = User::where('id', Auth::id())->first();

        // Cek apakah user sudah login
        if (!$user) {
            return redirect('/login')->with('error', 'You need to login first.');
        }

        // Initialize query
        $query = Reservation::with('restaurant')
            ->where('user_id', $user->id);

        // Check if date filters are provided
        if ($request->has('fromDate') && $request->fromDate) {
            $query->whereDate('reservationDate', '>=', $request->fromDate);
        }

        if ($request->has('toDate') && $request->toDate) {
            $query->whereDate('reservationDate', '<=', $request->toDate);
        }

        // Get results sorted by created_at
        $reservations = $query->orderBy('created_at', 'desc')->get();

        // Pass filter values to view for maintaining form state
        $fromDate = $request->fromDate ?? '';
        $toDate = $request->toDate ?? '';

        // Tampilkan di view history
        return view('history.orderHistory', compact('reservations', 'fromDate', 'toDate'));
    }

    public function cancelOrder($id)
    {
        // Temukan reservasi berdasarkan ID
        $reservation = Reservation::find($id);

        if (!$reservation) {
            return response()->json(['message' => 'Reservation not found'], 404);
        }

        // Ambil waktu reservasi dan waktu sekarang
        $reservationDateTime = Carbon::parse($reservation->reservationDate . ' ' . $reservation->reservationTime);
        $now = Carbon::now();

        // Validasi pembatalan minimal 1 jam sebelum reservasi
        if ($now->greaterThanOrEqualTo($reservationDateTime->subHours(1))) {
            return response()->json(['message' => 'Reservations can only be cancelled at least 1 hour before the scheduled time.'], 400);
        }

        // Periksa apakah reservasi sudah dibatalkan sebelumnya
        if ($reservation->reservationStatus === 'Cancelled') {
            return response()->json(['message' => 'Reservation is already cancelled'], 400);
        }

        // Update status reservasi menjadi "Cancelled"
        $reservation->reservationStatus = 'Cancelled';
        $reservation->save();

        //Save to Restaurant balance table
        $priceTotal = $reservation->priceTotal;
        $restaurant = Restaurant::where('id', $reservation->restaurant_id)->first();

        if ($restaurant && !is_null($priceTotal)) {
            // Cek apakah sudah ada saldo sebelumnya
            $existingBalance = RestaurantBalance::where('restaurant_id', $restaurant->id)->first();

            if ($existingBalance) {
                // Update saldo yang sudah ada
                $existingBalance->increment('restaurantBalance', $priceTotal);
            } else {
                // Insert saldo baru
                RestaurantBalance::create([
                    'restaurant_id' => $restaurant->id,
                    'restaurantBalance' => $priceTotal
                ]);
            }
        } else {
            \Log::info("Skipping balance update for reservation ID {$reservation->id} due to missing restaurant or null priceTotal.");
        }

        return response()->json([
            'message' => 'Reservation cancelled successfully',
        ]);
    }

    public function finishOrder($id)
    {
        $reservation = Reservation::findOrFail($id);
        // Log::info("Finish Order, Data : " . $reservation);
        $user = User::where('id', Auth::id())->first();

        if ($reservation->reservationStatus !== 'Arrived') {
            return response()->json([
                'message' => 'Only ongoing reservations can be cancelled!'
            ], 400);
        }

        $reservation->reservationStatus = 'Finished';
        $reservation->save();

        $earnedPoints = floor($reservation->priceTotal / 10000); // 1 point per Rp10.000

        // Log::info("Point yang didapatkan : " . $earnedPoints);
        if ($earnedPoints > 0) {
            PointLoyalty::updateOrCreate(
                ['user_id' => $user->id],
                ['point' => DB::raw("point + $earnedPoints")] // Tambahkan poin ke nilai sebelumnya
            );

            // Log::info("User {$user->id} mendapatkan {$earnedPoints} poin dari reservasi #{$reservation->id}");
        }

        //Save to Restaurant balance table
        $priceTotal = $reservation->priceTotal;
        $restaurant = Restaurant::where('id', $reservation->restaurant_id)->first();

        if ($restaurant && !is_null($priceTotal)) {
            // Cek apakah sudah ada saldo sebelumnya
            $existingBalance = RestaurantBalance::where('restaurant_id', $restaurant->id)->first();

            if ($existingBalance) {
                // Update saldo yang sudah ada
                $existingBalance->increment('restaurantBalance', $priceTotal);
            } else {
                // Insert saldo baru
                RestaurantBalance::create([
                    'restaurant_id' => $restaurant->id,
                    'restaurantBalance' => $priceTotal
                ]);
            }
        } else {
            \Log::info("Skipping balance update for reservation ID {$reservation->id} due to missing restaurant or null priceTotal.");
        }


        return response()->json([
            'message' => 'Your reservation has been Finished!',
            'new_status' => $reservation->reservationStatus
        ]);
    }

    public function restaurantReservations()
    {
        // Ambil ID restoran dari user yang sedang login
        $restaurant = Restaurant::where('user_id', Auth::user()->id)->first();

        // Ambil reservasi yang hanya dimiliki oleh restoran tersebut
        $reservations = Reservation::where('restaurant_id', $restaurant->id)->orderBy('created_at', 'desc')->get();

        // Kirim data ke view
        return view('restaurant.order.index', compact('reservations'));
    }

    public function confirmArrival($id){

        $reservation = Reservation::findOrFail($id);

        if ($reservation->reservationStatus !== 'On Going') {
            return response()->json([
                'message' => 'Only ongoing reservations can be confirmed as arrived!'
            ], 400);
        }
        $reservation->reservationStatus = 'Arrived';
        $reservation->save();

        return response()->json([
            'message' => 'Guest has been marked as Arrived!',
            'new_status' => $reservation->reservationStatus
        ]);
    }

}
