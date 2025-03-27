<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\Restaurant;
use App\Models\RestaurantBalance;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index()
    {
        $restaurant = Restaurant::where('user_id', Auth::user()->id)->first();

        // saldo
        $balance = RestaurantBalance::where('restaurant_id', $restaurant->id)->value('restaurantBalance');

        //list incoming
        $payments = Reservation::where('restaurant_id', $restaurant->id)
            ->whereNotNull('priceTotal')
            ->orderBy('updated_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        //list wd
        $withdraws = Payment::where('restaurant_id', $restaurant->id)
            ->orderBy('updated_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            // dd($withdraw);

        return view('restaurant.payment.index', compact('balance', 'payments', 'withdraws'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10000',
        ]);

        // Ambil restoran berdasarkan user yang login
        $restaurant = Restaurant::where('user_id', Auth::id())->first();

        if (!$restaurant) {
            return back()->with('error', 'Restaurant not found.');
        }

        // Ambil saldo saat ini
        $currentBalance = RestaurantBalance::where('restaurant_id', $restaurant->id)->sum('restaurantBalance');

        if ($request->amount > $currentBalance) {
            return back()->with('error', 'Insufficient balance.');
        }

        DB::beginTransaction();
        try {
            // Simpan data pembayaran (withdraw)
            $payment = Payment::create([
                'restaurant_id' => $restaurant->id,
                'amount' => $request->amount,
                'withdrawDate' => Carbon::now()->toDateString(),
                'withdrawTime' => Carbon::now()->toTimeString(),
                'status' => 'Pending', // Default status pending
            ]);

            // Kurangi saldo restaurantBalance
            RestaurantBalance::where('restaurant_id', $restaurant->id)
                ->decrement('restaurantBalance', $request->amount);

            DB::commit();

            return back()->with('success', 'Withdrawal request has been submitted.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }


}
