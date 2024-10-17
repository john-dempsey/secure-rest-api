<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

use App\Models\Order;

class OrderProcessedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Order $order, public string $status)
    {
    }
}
