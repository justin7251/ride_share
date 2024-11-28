<?php

namespace App\Models;

use App\Traits\HandlesTransactions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HandlesTransactions;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'verification_code',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'verification_code',
        'remember_token',
    ];

    protected $casts = [
        'phone_verified_at' => 'datetime',
    ];

    public function routeNotificationForTwilio()
    {
        return $this->phone;
    }

    public function rides()
    {
        return $this->hasMany(Ride::class);
    }

    public function driver()
    {
        return $this->hasOne(Driver::class);
    }
}
