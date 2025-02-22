<?php

namespace App\Models;

use App\Traits\HandlesTransactions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory, HandlesTransactions;

    protected $guarded = [];

    protected $casts = [
        'vehicle_info' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function rides()
    {
        return $this->hasMany(Ride::class);
    }

    public function location()
    {
        return $this->hasOne(DriverLocation::class);
    }
}
