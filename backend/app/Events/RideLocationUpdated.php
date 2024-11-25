<?php

namespace App\Events;

use App\Models\Ride;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RideLocationUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $ride;
    public $location;

    public function __construct(ride $ride, $location)
    {
        $this->ride = $ride;
        $this->location = $location;
    }

    public function broadcastOn()
    {
        return new Channel('ride.'.$this->ride->id);
    }

    public function broadcastAs()
    {
        return 'ride.location.updated';
    }
} 