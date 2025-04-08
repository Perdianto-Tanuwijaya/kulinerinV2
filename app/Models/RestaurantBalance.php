<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestaurantBalance extends Model
{
    use HasFactory;

    protected $fillable = ['restaurant_id', 'restaurantBalance'];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
}
