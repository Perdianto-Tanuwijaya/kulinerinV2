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
    protected $description = 'Automatically finish the reservations';

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
        return Command::SUCCESS;
    }
}
