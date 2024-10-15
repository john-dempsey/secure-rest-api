<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

use App\Events\OrderProcessed;
use App\Notifications\OrderNotification;

class OrderProcessedListener implements ShouldQueue
{
    public function __construct()
    {
        //
    }

    public function handle(OrderProcessed $event): void
    {
        Mail::to($event->order->customer->email)
            ->send(new OrderProcessed($event->order, $event->status));

        // $customer = $order->customer;
        // $customer->notify(new OrderNotification($event->order, $event->status));
    }

    public function failed(OrderProcessed $event, Throwable $exception): void
    {
        Log::error('OrderProcessedListener failed', [
            'order' => $event->order->id,
            'status' => $event->status,
            'exception' => $exception->getMessage(),
        ]);
    }
}
