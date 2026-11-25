<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'ref', 'first_name', 'last_name', 'email', 'phone',
        'address', 'city', 'postal', 'notes',
        'shipping_method', 'payment',
        'subtotal', 'shipping', 'total',
        'payment_status', 'payment_intent_id', 'payment_method_type', 'payment_ref',
        'email_sent',
    ];

    protected $casts = [
        'subtotal'    => 'integer',
        'shipping'    => 'integer',
        'total'       => 'integer',
        'email_sent'  => 'boolean',
    ];

    /**
     * Display label for the payment method chosen at checkout (the radio button).
     * Kept in the original `payment` column.
     */
    public function paymentLabel(): string
    {
        return match ($this->payment) {
            'cod'   => 'Cash on Delivery',
            'gcash' => 'GCash',
            'bank'  => 'Bank Transfer',
            default => ucfirst($this->payment ?? 'Unknown'),
        };
    }

    /**
     * Human-readable status badge.
     */
    public function statusBadge(): string
    {
        return match ($this->payment_status) {
            'paid'                => 'Paid',
            'succeeded'           => 'Paid',
            'pending_payment'     => 'Awaiting Payment',
            'awaiting_payment_method' => 'Awaiting Payment',
            'awaiting_next_action'    => 'Awaiting Action',
            'processing'          => 'Processing',
            'failed'              => 'Failed',
            'cancelled'            => 'Cancelled',
            default               => ucfirst($this->payment_status ?? 'Pending'),
        };
    }

    /**
     * True when this order has been successfully paid online.
     */
    public function isPaid(): bool
    {
        return in_array($this->payment_status, ['paid', 'succeeded'], true);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
