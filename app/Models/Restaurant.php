<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'restaurantName',
        'restaurantPhoneNumber',
        'restaurantCity',
        'restaurantAddress',
        'restaurantDescription',
        'restaurantStyle',
        'restaurantImage'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ratingRestaurants()
    {
        return $this->hasMany(RatingRestaurant::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function menuRestaurants()
    {
        return $this->hasMany(MenuRestaurant::class);
    }
    public function tableRestaurants()
    {
        return $this->hasMany(TableRestaurant::class);
    }

    public function operationalHours()
    {
        return $this->hasMany(OperationalHour::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function restaurantBalance()
    {
        return $this->hasOne(RestaurantBalance::class);
    }
}
