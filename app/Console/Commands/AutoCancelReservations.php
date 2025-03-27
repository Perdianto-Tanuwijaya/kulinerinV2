<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\ReservationController;
use Carbon\Carbon;
use App\Models\Reservation;
use App\Models\Restaurant;
use App\Models\RestaurantBalance;
use App\Models\PointLoyalty;
use Illuminate\Support\Facades\Log;

class AutoCancelReservations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reservations:auto-cancel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically cancel expired reservations';

    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $expiredReservations = Reservation::where('reservationStatus', 'On Going')
            ->whereRaw("TIMESTAMP(reservationDate, reservationTime) < ?", [Carbon::now()->subMinutes(29)->format('Y-m-d H:i:s')]) // ini buat set auto cancelnya kl dah expired brp lama
            ->get();

        $count = 0;

        foreach ($expiredReservations as $reservation) {
            $reservation->reservationStatus = 'Cancelled';
            $reservation->save();
            $count++;
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

        // Log hasilnya
        Log::info("Auto cancel job executed. Data : " . $expiredReservations);
    }
}
