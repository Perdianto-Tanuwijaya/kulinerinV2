<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'amount',
        'withdrawDate',
        'withdrawTime',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

}
