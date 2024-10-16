<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

use App\Models\Order;

class OrderProcessed extends Mailable implements ShouldQueue
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
        $this->order->load('customer');
        $this->order->load('products');
        return new Content(
            view: 'emails.orders.processed',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
