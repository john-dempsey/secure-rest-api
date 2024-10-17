<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

use App\Events\OrderProcessedEvent;
use App\Notifications\OrderProcessedNotification;

use Illuminate\Support\Facades\Mail;
use App\Mail\OrderProcessedMailable;

use Throwable;

class OrderProcessedListener implements ShouldQueue
{
    public function __construct()
    {
        //
    }

    public function handle(OrderProcessedEvent $event): void
    {
        Mail::to($event->order->customer->email)
            ->send(new OrderProcessedMailable($event->order, $event->status));

        // $customer = $event->order->customer;
        // $customer->notify(new OrderProcessedNotification($event->order, $event->status));
    }

    public function failed(OrderProcessedEvent $event, Throwable $exception): void
    {
        Log::error('OrderProcessedListener failed', [
            'order' => $event->order->id,
            'status' => $event->status,
            'exception' => $exception->getMessage(),
        ]);
    }
}
