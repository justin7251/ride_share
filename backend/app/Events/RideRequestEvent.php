<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RideRequestEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $rideRequest;
    public $activeDrivers;

    /**
     * Create a new event instance.
     */
    public function __construct($rideRequest, $activeDrivers)
    {
        $this->rideRequest = $rideRequest;
        $this->activeDrivers = $activeDrivers;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn()
    {
        return new Channel('available-rides');
    }

    public function broadcastWith()
    {
        return [
            'type' => 'NEW_RIDE_REQUEST',
            'ride' => [
                'id' => $this->rideRequest->id,
                'pickup' => $this->rideRequest->origin,
                'destination' => $this->rideRequest->destination,
                'pickup_lat' => $this->rideRequest->pickup_lat,
                'pickup_lng' => $this->rideRequest->pickup_lng,
            ],
            'activeDrivers' => $this->activeDrivers->map(function($driver) {
                return [
                    'id' => $driver->id,
                    'name' => $driver->name,
                    'car_model' => $driver->car_model,
                    'car_color' => $driver->car_color,
                    'plate_number' => $driver->plate_number
                ];
            })
        ];
    }
}
