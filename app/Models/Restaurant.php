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
        return $this->belongsTo(User::class, 'user_id');
    }

    public function ratingRestaurants()
    {
        return $this->hasMany(RatingRestaurant::class, 'restaurant_id');
    }

    public function menuRestaurants()
    {
        return $this->hasMany(MenuRestaurant::class, 'restaurant_id');
    }
    public function tableRestaurants()
    {
        return $this->hasMany(TableRestaurant::class, 'restaurant_id');
    }

    public function operationalHours()
    {
        return $this->hasMany(OperationalHour::class, 'restaurant_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'restaurant_id');
    }

    public function restaurantBalances()
    {
        return $this->hasMany(RestaurantBalance::class, 'restaurant_id');
    }
}
