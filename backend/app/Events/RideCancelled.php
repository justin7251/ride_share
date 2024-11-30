<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RideCancelled implements ShouldBroadcast
{
    public $ride;
    public $reason;

    public function __construct(Ride $ride, $reason = null)
    {
        $this->ride = $ride;
        $this->reason = $reason;
    }

    public function broadcastOn()
    {
        return new Channel('available-rides');
    }

    public function broadcastWith()
    {
        return [
            'ride' => [
                'id' => $this->ride->id,
                'pickup' => $this->ride->origin,
                'destination' => $this->ride->destination
            ],
            'reason' => $this->reason
        ];
    }
}
