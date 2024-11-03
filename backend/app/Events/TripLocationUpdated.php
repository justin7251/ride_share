<?php

namespace App\Events;

use App\Models\Trip;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TripLocationUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $trip;
    public $location;

    public function __construct(Trip $trip, $location)
    {
        $this->trip = $trip;
        $this->location = $location;
    }

    public function broadcastOn()
    {
        return new Channel('trip.'.$this->trip->id);
    }
} 