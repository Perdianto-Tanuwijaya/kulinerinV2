<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'restaurant_id', 'table_restaurant_id', 'guest', 'tableType', 'restaurantName',
        'reservationDate', 'reservationTime', 'reservationStatus', 'bookingCode',
        'menuData', 'priceTotal'
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function ratingRestaurant()
    {
        return $this->belongsTo(RatingRestaurant::class, 'rating_id');
    }

    public function tableRestaurant()
    {
        return $this->belongsTo(TableRestaurant::class, 'table_restaurant_id', 'id');
    }
}
