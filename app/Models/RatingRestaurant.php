<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RatingRestaurant extends Model
{
    use HasFactory;
    protected $fillable = ['restaurant_id', 'reservation_id', 'user_id', 'score', 'review'];
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
}
