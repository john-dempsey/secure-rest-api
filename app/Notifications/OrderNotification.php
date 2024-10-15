<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use App\Models\Order;

class OrderNotification extends Notification
{
    use Queueable;

    public function __construct(public Order $order, public string $status)
    {
        //
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Order notification.')
                    ->greeting('Hello!')
                    ->line('Your order number ' . $this->order->id . ' has been ' . $this->status)
                    ->line('Thank you for using our business!');
    }
}
