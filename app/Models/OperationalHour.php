<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperationalHour extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'day',
        'open_time',
        'close_time'
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
}
