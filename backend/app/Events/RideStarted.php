<?php

namespace App\Events;

use App\Models\Ride;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RideStarted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $ride;

    public function __construct(ride $ride)
    {
        $this->ride = $ride;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('ride.'.$this->ride->id);
    }

    public function broadcastAs()
    {
        return 'ride.started';
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->ride->id,
            'status' => $this->ride->status,
        ];
    }
} 