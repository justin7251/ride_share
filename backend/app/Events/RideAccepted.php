<?php

namespace App\Events;

use App\Models\Ride;
use App\Models\User;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RideAccepted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $ride;
    public $driver;

    public function __construct(Ride $ride, User $driver)
    {
        $this->ride = $ride;
        $this->driver = $driver;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('ride.' . $this->ride->id);
    }

    public function broadcastWith()
    {
        return [
            'ride' => [
                'id' => $this->ride->id,
                'pickup' => $this->ride->origin,
                'destination' => $this->ride->destination
            ],
            'driver' => [
                'id' => $this->driver->id,
                'name' => $this->driver->name,
                'car_model' => $this->driver->car_model,
                'car_color' => $this->driver->car_color
            ]
        ];
    }
} 