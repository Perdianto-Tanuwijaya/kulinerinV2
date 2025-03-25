<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function index()
    {
        $restaurant = Restaurant::where('user_id', Auth::user()->id)->first();

        $payments = Reservation::where('restaurant_id', $restaurant->id)
            ->whereNotNull('priceTotal')
            ->latest('updated_at')
            ->latest('created_at')
            ->get();
        // dd($payments);

        $totalAmount = $payments
            ->whereIn('reservationStatus', ['Finished', 'Cancelled'])
            ->sum('priceTotal');
        // dd($totalAmount);

        return view('restaurant.payment.index', compact('payments', 'totalAmount'));
    }

}
