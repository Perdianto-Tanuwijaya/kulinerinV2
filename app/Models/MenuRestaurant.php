<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuRestaurant extends Model
{

    use HasFactory;

    protected $fillable = ['menuName', 'category', 'menuImage', 'menuprice', 'isAVailable', 'description'];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
}
