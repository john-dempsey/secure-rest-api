<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use App\Mail\OrderProcessedMail;
use App\Models\Order;

class OrderProcessedNotification extends Notification implements ShouldQueue
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
                    ->subject('Order details')
                    ->from('jane@bloggs.com', 'Jane Bloggs')
                    ->view(
                        'emails.orders.processed',
                        ['order' => $this->order, 'status' => $this->status]
                    );
        
    }
}
