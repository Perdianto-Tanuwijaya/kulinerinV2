<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'reservation_id',
        'amount',
        'paymentDate',
        'paymentTime',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
}
