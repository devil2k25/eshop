<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CartController extends Controller
{
    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function getCart(): Cart
    {
        if (auth()->check()) {
            return Cart::firstOrCreate(['user_id' => auth()->id()]);
        }

        return Cart::firstOrCreate(['session_id' => session()->getId()]);
    }

    // ─── Pages ────────────────────────────────────────────────────────────────

    public function index(): Response
    {
        $cart = $this->getCart();
        $cart->load(['items.product.primaryImage', 'items.variant', 'coupon']);

        $items = $cart->items->map(fn ($item) => [
            'id'       => $item->id,
            'product'  => [
                'id'    => $item->product->id,
                'name'  => $item->product->name,
                'slug'  => $item->product->slug,
                'price' => $item->product->price,
                'stock' => $item->product->stock,
                'image' => $item->product->primaryImage?->image,
            ],
            'variant'  => $item->variant
                ? [
                    'id'    => $item->variant->id,
                    'name'  => $item->variant->name,
                    'price' => $item->variant->price,
                    'stock' => $item->variant->stock,
                ]
                : null,
            'quantity' => $item->quantity,
            'subtotal' => ($item->variant?->price ?? $item->product->price) * $item->quantity,
        ]);

        $subtotal             = $items->sum('subtotal');
        $discount             = 0;
        $couponCode           = null;
        $freeShippingThreshold = (float) Setting::get('free_shipping_threshold', 100);
        $shippingCost         = (float) Setting::get('default_shipping_cost', 10);

        if ($cart->coupon) {
            $couponCode = $cart->coupon->code;
            $discount   = $cart->coupon->calculateDiscount($subtotal);
        }

        $shipping = ($subtotal - $discount) >= $freeShippingThreshold ? 0 : $shippingCost;
        $total    = $subtotal - $discount + $shipping;

        return Inertia::render('Shop/Cart', compact(
            'items',
            'subtotal',
            'discount',
            'shipping',
            'total',
            'couponCode',
            'freeShippingThreshold'
        ));
    }

    // ─── Actions ──────────────────────────────────────────────────────────────

    public function add(Request $request): RedirectResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id',
            'quantity'   => 'required|integer|min:1|max:99',
        ]);

        $cart = $this->getCart();

        $existing = $cart->items()
            ->where('product_id', $request->product_id)
            ->where('product_variant_id', $request->variant_id)
            ->first();

        if ($existing) {
            $existing->update([
                'quantity' => min($existing->quantity + $request->quantity, 99),
            ]);
        } else {
            $cart->items()->create([
                'product_id'         => $request->product_id,
                'product_variant_id' => $request->variant_id,
                'quantity'           => $request->quantity,
            ]);
        }

        return back()->with('success', 'Added to cart.');
    }

    public function update(Request $request, CartItem $item): RedirectResponse
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:99',
        ]);

        $item->update(['quantity' => $request->quantity]);

        return back()->with('success', 'Cart updated.');
    }

    public function remove(CartItem $item): RedirectResponse
    {
        $item->delete();

        return back()->with('success', 'Item removed.');
    }

    public function applyCoupon(Request $request): RedirectResponse
    {
        $request->validate(['code' => 'required|string']);

        $coupon = Coupon::where('code', strtoupper($request->code))->first();
        $cart   = $this->getCart();
        $cart->load('items.product', 'items.variant');

        $subtotal = $cart->items->sum(
            fn ($i) => ($i->variant?->price ?? $i->product->price) * $i->quantity
        );

        if (! $coupon || ! $coupon->isValid($subtotal)) {
            return back()->with('error', 'Invalid or expired coupon code.');
        }

        $cart->update(['coupon_id' => $coupon->id]);

        return back()->with('success', 'Coupon applied.');
    }

    public function removeCoupon(): RedirectResponse
    {
        $cart = $this->getCart();
        $cart->update(['coupon_id' => null]);

        return back()->with('success', 'Coupon removed.');
    }
}
