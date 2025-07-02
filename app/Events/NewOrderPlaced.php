<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewOrderPlaced implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $order;

    public function __construct($order)
    {
        $this->order = $order;
    }
    public function broadcastOn()
    {
        // return new Channel('orders');
        return ['orders'];
        // return ['my-channel'];

    }

    public function broadcastAs()
    {
        return 'new.order.placed';
        // return 'my-event';
    }

    
}
