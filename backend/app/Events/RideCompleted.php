<?php

namespace App\Events;

use App\Models\Ride;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RideCompleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $ride;

    public function __construct(Ride $ride)
    {
        $this->ride = $ride;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('ride.' . $this->ride->id);
    }

    public function broadcastAs()
    {
        return 'ride.completed';
    }

    public function broadcastWith()
    {
        return [
            'ride' => [
                'id' => $this->ride->id,
                'status' => 'completed',
                'origin' => $this->ride->origin,
                'destination' => $this->ride->destination,
                'completed_at' => now()->toDateTimeString()
            ]
        ];
    }
} 