<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'coupon_id',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class)->with(['product', 'variant']);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    public function getSubtotalAttribute(): float
    {
        return $this->items->sum(function (CartItem $item) {
            $price = $item->variant?->price ?? $item->product?->price ?? 0;

            return $price * $item->quantity;
        });
    }

    public function getTotalAttribute(): float
    {
        $subtotal = $this->subtotal;
        $discount = 0.0;
        $shipping = 0.0;

        if ($this->coupon && $this->coupon->isValid($subtotal)) {
            $discount = $this->coupon->calculateDiscount($subtotal);
        }

        return max(0, $subtotal - $discount + $shipping);
    }
}
