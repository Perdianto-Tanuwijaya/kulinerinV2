<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdvertisementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('advertisements')->insert([
            [
                'id' => 1,
                'adImage' => 'advertisement\imageRestaurant3.avif, advertisement\imageRestaurant4.jpg, advertisement\imageRestaurant6.webp',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
