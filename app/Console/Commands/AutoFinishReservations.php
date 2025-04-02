<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\ReservationController;
use Carbon\Carbon;
use App\Models\Reservation;
use App\Models\Restaurant;
use App\Models\RestaurantBalance;
use App\Models\PointLoyalty;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutoFinishReservations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reservations:auto-finish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically finish reservations that have passed 24 hours after reservation time';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info("Starting auto-finish process...");

        // Ambil reservasi yang sudah lebih dari 24 jam setelah reservationTime dan masih "Arrived"
        $reservations = Reservation::where('reservationStatus', 'Arrived')
            ->whereRaw("STR_TO_DATE(CONCAT(reservationDate, ' ', reservationTime), '%Y-%m-%d %H:%i:%s') <= ?", [Carbon::now()->subDay()])
            ->get();

        Log::info("Reservations Found: " . $reservations->count());

        if ($reservations->isEmpty()) {
            $this->info("No reservations need to be finished.");
            return Command::SUCCESS;
        }

        foreach ($reservations as $reservation) {
            $this->info("Finishing reservation ID: {$reservation->id}");

            // Update status reservasi menjadi "Finished"
            $reservation->reservationStatus = 'Finished';
            $reservation->save();

            $user = User::find($reservation->user_id);
            $earnedPoints = floor($reservation->priceTotal / 10000); // 1 point per Rp10.000

            if ($earnedPoints > 0 && $user) {
                PointLoyalty::updateOrCreate(
                    ['user_id' => $user->id],
                    ['point' => DB::raw("point + $earnedPoints")]
                );
                $this->info("User ID {$user->id} earned {$earnedPoints} points.");
            }

            // Update saldo restoran
            $priceTotal = $reservation->priceTotal;
            $restaurant = Restaurant::find($reservation->restaurant_id);

            if ($restaurant && !is_null($priceTotal)) {
                $existingBalance = RestaurantBalance::where('restaurant_id', $restaurant->id)->first();

                if ($existingBalance) {
                    $existingBalance->increment('restaurantBalance', $priceTotal);
                } else {
                    RestaurantBalance::create([
                        'restaurant_id' => $restaurant->id,
                        'restaurantBalance' => $priceTotal
                    ]);
                }
                $this->info("Updated balance for restaurant ID: {$restaurant->id}");
            }
        }

        $this->info("Auto-finish process completed.");
        return Command::SUCCESS;
    }
}
