<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderUpdateMail extends Mailable
{
    use Queueable, SerializesModels;

    public $orderNum;
    public $name;
    public $orderStatus;

    /**
     * Create a new message instance.
     */
    public function __construct($name, $orderNum, $orderStatus)
    {
        $this->name = $name;
        $this->orderNum = $orderNum;
        $this->orderStatus = $orderStatus;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Order updated',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.order_update',
                  with: [
                    'name' => $this->name,
                    'orderNum' => $this->orderNum,
                    'orderStatus' => $this->orderStatus,
                ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
