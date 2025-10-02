<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Donation extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount',
        'currency',
        'type',
        'user_id',
        'event_id',
        'message',
        'anonymous',
        'stripe_payment_intent_id',
        'stripe_charge_id',
        'payment_status',
        'payment_method',
        'paid_at',
        'refund_status',
        'refunded_amount',
        'refund_reason',
        'refunded_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'refunded_amount' => 'decimal:2',
        'anonymous' => 'boolean',
        'paid_at' => 'datetime',
        'refunded_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Enums
    const TYPES = [
        'tree_planting' => 'Tree Planting',
        'maintenance' => 'Maintenance',
        'awareness_campaign' => 'Awareness Campaign'
    ];

    const PAYMENT_STATUSES = [
        'pending' => 'Pending',
        'processing' => 'Processing',
        'succeeded' => 'Succeeded',
        'failed' => 'Failed',
        'canceled' => 'Canceled'
    ];

    const REFUND_STATUSES = [
        'none' => 'No Refund',
        'requested' => 'Refund Requested',
        'processing' => 'Refund Processing',
        'succeeded' => 'Refunded',
        'failed' => 'Refund Failed'
    ];

    protected $attributes = [
        'currency' => 'TND',
        'payment_status' => 'pending',
        'refund_status' => 'none'
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function relatedEvent(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    // Accessors
    public function getFormattedAmountAttribute(): string
    {
        return number_format((float) $this->amount, 2) . ' ' . strtoupper($this->currency);
    }

    public function getTypeNameAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function getPaymentStatusNameAttribute(): string
    {
        return self::PAYMENT_STATUSES[$this->payment_status] ?? $this->payment_status;
    }

    public function getRefundStatusNameAttribute(): string
    {
        return self::REFUND_STATUSES[$this->refund_status] ?? $this->refund_status;
    }

    // Scopes
    public function scopeSuccessful($query)
    {
        return $query->where('payment_status', 'succeeded');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForEvent($query, $eventId)
    {
        return $query->where('event_id', $eventId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Methods
    public function canRefund(): bool
    {
        return $this->payment_status === 'succeeded' && 
               $this->refund_status === 'none' &&
               $this->created_at->diffInDays(now()) <= 30; // 30 days refund policy
    }

    public function getTotalRefundableAmount(): float
    {
        return $this->amount - ($this->refund_amount ?? 0);
    }

    // Stripe payment methods
    public function createPaymentIntent()
    {
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        $paymentIntent = \Stripe\PaymentIntent::create([
            'amount' => $this->amount * 100, // Stripe expects amount in cents
            'currency' => strtolower($this->currency),
            'metadata' => [
                'donation_id' => $this->id,
                'user_id' => $this->user_id,
                'type' => $this->type,
            ],
        ]);

        // Save the PaymentIntent ID
        $this->update([
            'stripe_payment_intent_id' => $paymentIntent->id
        ]);

        return $paymentIntent;
    }

    public function processRefund($amount, $reason = null)
    {
        if (!$this->stripe_charge_id) {
            throw new \Exception('No charge ID found for this donation');
        }

        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        $refund = \Stripe\Refund::create([
            'charge' => $this->stripe_charge_id,
            'amount' => $amount * 100, // Stripe expects amount in cents
            'reason' => 'requested_by_customer',
            'metadata' => [
                'donation_id' => $this->id,
                'refund_reason' => $reason,
            ],
        ]);

        // Update donation refund status
        $this->update([
            'refund_status' => 'pending',
            'refunded_amount' => $amount,
            'refund_reason' => $reason,
            'refunded_at' => now(),
        ]);

        return $refund;
    }
}
