<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'google_id',
        'points',
        'role' //IMPORTANT FOR RESTAURANT AND CUSTOMER DIFFERENTIATION
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class, 'user_id');
    }
    public function ratingRestaurants()
    {
        return $this->hasMany(RatingRestaurant::class, 'user_id');
    }
    public function redemptions()
    {
        return $this->hasMany(Redemption::class);
    }
    public function pointLoyalties()
    {
        return $this->hasOne(PointLoyalty::class, 'user_id', 'id');
    }
    public function advertisements()
    {
        return $this->hasMany(Advertisement::class);
    }
}
