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
use Illuminate\Support\Facades\Log;

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
            'bankName' => 'required|string',
            'bankAccount' => 'required|string',
        ]);
        Log::info("Masuk: " . $request->bankName);

        $amount = str_replace(',', '', $request->amount);
        $amount = (float) $amount;

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
            $payment = new Payment();
            $payment->restaurant_id = $restaurant->id;
            $payment->amount = $amount;
            $payment->bankName = $request->bankName;
            $payment->bankAccount = $request->bankAccount;
            $payment->withdrawDate = Carbon::now()->toDateString();
            $payment->withdrawTime = Carbon::now()->toTimeString();
            $payment->status = 'Pending';
            $payment->save();

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

    // Admin
    public function show(Request $request){

        $withdraws = Payment::query();

        if ($request->has('search') && !empty($request->search)) {
            $withdraws->whereHas('restaurant', function ($query) use ($request) {
                $query->where('restaurantName', 'LIKE', '%' . $request->search . '%');
            });
        }

        $withdraws = $withdraws->latest()->paginate(10);

        return view ('admin.payment.index', compact('withdraws'));
    }

    public function updateStatus(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);
        $restaurant = Restaurant::findOrFail($payment->restaurant_id);

        if ($request->status === 'Rejected') {
            // Ambil saldo saat ini
            $balance = RestaurantBalance::where('restaurant_id', $restaurant->id)->first();

            if ($balance) {
                // Tambahkan kembali jumlah yang ditarik ke saldo restoran
                $balance->restaurantBalance += $payment->amount;
                $balance->save();
            } else {
                // Jika saldo belum ada, buat record baru
                RestaurantBalance::create([
                    'restaurant_id' => $restaurant->id,
                    'restaurantBalance' => $payment->amount
                ]);
            }
        }

        // Update status pembayaran
        $payment->status = $request->status;
        $payment->save();

        return response()->json(['message' => 'Payment status updated successfully.']);
    }



}
