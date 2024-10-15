<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderProcessed extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order, public string $status)
    {
        //
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Order Processed',
            from: new Address('jane@bloggs.com', 'Jane Bloggs'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.orders.processed',
            text: 'emails.orders.processed_plain',
            data: [
                'orderId' => $this->order->id,
                'orderTotal' => $this->order->totalAmount(),
                'orderStatus' => $this->status,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
