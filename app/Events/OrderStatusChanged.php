<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStatusChanged implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param  array<string, mixed>  $order
     */
    public function __construct(public array $order) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('pos-display'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'order.status_changed';
    }
}
