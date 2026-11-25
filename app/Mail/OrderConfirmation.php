<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Order Confirmation — {$this->order->ref} | Danielle's Crafted Pizza",
            from: config('mail.from.address', 'orders@daniellescraftedpizza.example'),
            replyTo: [config('mail.from.address', 'orders@daniellescraftedpizza.example')],
        );
    }

    public function content(): Content
    {
        return new Content(
            html: 'emails.order-confirmation',
            with: [
                'order'      => $this->order,
                'items'      => $this->order->items,
                'subtotal'   => $this->order->subtotal,
                'shipping'   => $this->order->shipping,
                'total'      => $this->order->total,
                'firstName'  => $this->order->first_name,
                'statusLabel' => $this->statusLabel(),
                'paymentLabel' => $this->paymentLabel(),
                'shippingLabel' => $this->shippingLabel(),
            ],
        );
    }

    private function statusLabel(): string
    {
        return match ($this->order->payment_status) {
            'paid', 'succeeded'           => 'Paid',
            'pending_cod'                  => 'Cash on Delivery',
            'failed', 'cancelled'          => 'Failed',
            default                         => 'Pending',
        };
    }

    private function paymentLabel(): string
    {
        $methodLabel = ucfirst($this->order->payment_method_type ?? 'cod');
        if ($this->order->payment_method_type === 'gcash') $methodLabel = 'GCash';
        return match ($this->order->payment) {
            'cod'    => 'Cash on Delivery',
            'online' => "Online Payment ({$methodLabel})",
            default  => ucfirst($this->order->payment ?? 'Unknown'),
        };
    }

    private function shippingLabel(): string
    {
        return match ($this->order->shipping_method) {
            'express'  => 'Express Food Dispatch',
            'standard' => 'Standard Delivery',
            default    => 'Delivery',
        };
    }
}
