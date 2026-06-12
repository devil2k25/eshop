<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'value',
        'minimum_order',
        'maximum_discount',
        'usage_limit',
        'usage_count',
        'per_user_limit',
        'is_active',
        'starts_at',
        'expires_at',
    ];

    protected $casts = [
        'value'            => 'decimal:2',
        'minimum_order'    => 'decimal:2',
        'maximum_discount' => 'decimal:2',
        'usage_limit'      => 'integer',
        'usage_count'      => 'integer',
        'per_user_limit'   => 'integer',
        'is_active'        => 'boolean',
        'starts_at'        => 'datetime',
        'expires_at'       => 'datetime',
    ];

    /**
     * Determine whether this coupon is valid for the given order total.
     */
    public function isValid(float $orderTotal): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->starts_at && $this->starts_at->isFuture()) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        if ($this->usage_limit !== null && $this->usage_count >= $this->usage_limit) {
            return false;
        }

        if ($this->minimum_order !== null && $orderTotal < $this->minimum_order) {
            return false;
        }

        return true;
    }

    /**
     * Calculate the discount amount for the given subtotal.
     */
    public function calculateDiscount(float $subtotal): float
    {
        if ($this->type === 'percentage') {
            $discount = $subtotal * ($this->value / 100);

            if ($this->maximum_discount !== null) {
                $discount = min($discount, (float) $this->maximum_discount);
            }

            return round($discount, 2);
        }

        // Fixed discount
        return round(min((float) $this->value, $subtotal), 2);
    }
}
