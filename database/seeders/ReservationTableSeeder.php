<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ReservationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('reservations')->insert([
            [
                'id' => 1,
                'user_id' => 12,
                'restaurant_id' => 2,
                'table_restaurant_id' => 4,
                'guest' => 1,
                'restaurantName' => 'Anigre - Sheraton Grand Jakarta Gandaria City Hotel',
                'reservationDate' => '2025-03-25',
                'reservationTime' => '10:00:00',
                'reservationStatus' => 'Finished',
                'bookingCode' => 'NERCB509722IN',
                'menuData' => '1x Fried Calamari - Rp 26838, 2x Mushroom Risotto - Rp 31683, 2x Beef Burger - Rp 42163, 2x Tiramisu - Rp 24996',
                'priceTotal' => 224522,
                'created_at' => Carbon::parse('2025-03-25 02:45:36'),
                'updated_at' => Carbon::parse('2025-03-25 02:46:49'),
            ],
            [
                'id' => 2,
                'user_id' => 12,
                'restaurant_id' => 2,
                'table_restaurant_id' => 4,
                'guest' => 1,
                'restaurantName' => 'Anigre - Sheraton Grand Jakarta Gandaria City Hotel',
                'reservationDate' => '2025-03-25',
                'reservationTime' => '10:00:00',
                'reservationStatus' => 'Finished',
                'bookingCode' => 'NER9548D44EIN',
                'menuData' => NULL,
                'priceTotal' => NULL,
                'created_at' => Carbon::parse('2025-03-25 02:50:59'),
                'updated_at' => Carbon::parse('2025-03-25 02:52:16'),
            ],
            [
                'id' => 3,
                'user_id' => 12,
                'restaurant_id' => 2,
                'table_restaurant_id' => 4,
                'guest' => 1,
                'restaurantName' => 'Anigre - Sheraton Grand Jakarta Gandaria City Hotel',
                'reservationDate' => '2025-03-25',
                'reservationTime' => '10:00:00',
                'reservationStatus' => 'Cancelled',
                'bookingCode' => 'NERB44F99DAIN',
                'menuData' => '1x Garlic Bread with Cheese - Rp 40323',
                'priceTotal' => 40323,
                'created_at' => Carbon::parse('2025-03-25 02:51:36'),
                'updated_at' => Carbon::parse('2025-03-25 02:52:22'),
            ],
            [
                'id' => 4,
                'user_id' => 12,
                'restaurant_id' => 5,
                'table_restaurant_id' => 9,
                'guest' => 1,
                'restaurantName' => 'BLANCO Par Mandif',
                'reservationDate' => '2025-03-25',
                'reservationTime' => '10:15:00',
                'reservationStatus' => 'Finished',
                'bookingCode' => 'NERC9F40713IN',
                'menuData' => '1x Bruschetta - Rp 41267, 2x Chicken Alfredo - Rp 34443, 1x Chocolate Lava Cake - Rp 15505',
                'priceTotal' => 125658,
                'created_at' => Carbon::parse('2025-03-25 03:11:17'),
                'updated_at' => Carbon::parse('2025-03-25 03:11:36'),
            ],
            [
                'id' => 5,
                'user_id' => 12,
                'restaurant_id' => 1,
                'table_restaurant_id' => 1,
                'guest' => 1,
                'restaurantName' => 'Lawry\'s The Prime Rib Jakarta',
                'reservationDate' => '2025-03-25',
                'reservationTime' => '20:15:00',
                'reservationStatus' => 'On Going',
                'bookingCode' => 'NER9ADA6DB1IN',
                'menuData' => '4x Spring Rolls - Rp 10000, 2x Garlic Bread - Rp 45000',
                'priceTotal' => 130000,
                'created_at' => Carbon::parse('2025-03-25 13:12:29'),
                'updated_at' => Carbon::parse('2025-03-25 13:12:29'),
            ],
        ]);
    }
}
