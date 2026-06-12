<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class CheckoutController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function getCart(): Cart
    {
        return Cart::firstOrCreate(['user_id' => auth()->id()]);
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
                'image' => $item->product->primaryImage?->image,
            ],
            'variant'  => $item->variant
                ? [
                    'id'    => $item->variant->id,
                    'name'  => $item->variant->name,
                    'price' => $item->variant->price,
                ]
                : null,
            'quantity' => $item->quantity,
            'subtotal' => ($item->variant?->price ?? $item->product->price) * $item->quantity,
        ]);

        $subtotal              = $items->sum('subtotal');
        $discount              = 0;
        $couponCode            = null;
        $freeShippingThreshold = (float) Setting::get('free_shipping_threshold', 100);
        $shippingCost          = (float) Setting::get('default_shipping_cost', 10);

        if ($cart->coupon) {
            $couponCode = $cart->coupon->code;
            $discount   = $cart->coupon->calculateDiscount($subtotal);
        }

        $shipping = ($subtotal - $discount) >= $freeShippingThreshold ? 0 : $shippingCost;
        $total    = $subtotal - $discount + $shipping;

        $addresses = auth()->user()
            ->addresses()
            ->orderByDesc('is_default')
            ->get();

        return Inertia::render('Shop/Checkout', compact(
            'items',
            'subtotal',
            'discount',
            'shipping',
            'total',
            'couponCode',
            'addresses'
        ));
    }

    // ─── Actions ──────────────────────────────────────────────────────────────

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'payment_method'   => 'required|string|in:cod,stripe,paypal',
            'address_id'       => 'nullable|exists:addresses,id',
            'name'             => 'required_without:address_id|string|max:255',
            'phone'            => 'required_without:address_id|string|max:50',
            'address_line1'    => 'required_without:address_id|string|max:255',
            'address_line2'    => 'nullable|string|max:255',
            'city'             => 'required_without:address_id|string|max:100',
            'state'            => 'required_without:address_id|string|max:100',
            'zip_code'         => 'required_without:address_id|string|max:20',
            'country'          => 'required_without:address_id|string|max:100',
            'notes'            => 'nullable|string|max:1000',
        ]);

        $cart = $this->getCart();
        $cart->load(['items.product', 'items.variant', 'coupon']);

        if ($cart->items->isEmpty()) {
            return back()->with('error', 'Your cart is empty.');
        }

        // Resolve shipping address
        if ($request->address_id) {
            $address = Address::where('id', $request->address_id)
                ->where('user_id', auth()->id())
                ->firstOrFail();

            $shippingData = [
                'shipping_name'         => $address->name,
                'shipping_phone'        => $address->phone,
                'shipping_address_line1' => $address->address_line1,
                'shipping_address_line2' => $address->address_line2,
                'shipping_city'         => $address->city,
                'shipping_state'        => $address->state,
                'shipping_zip_code'     => $address->zip_code,
                'shipping_country'      => $address->country,
            ];
        } else {
            $shippingData = [
                'shipping_name'         => $request->name,
                'shipping_phone'        => $request->phone,
                'shipping_address_line1' => $request->address_line1,
                'shipping_address_line2' => $request->address_line2,
                'shipping_city'         => $request->city,
                'shipping_state'        => $request->state,
                'shipping_zip_code'     => $request->zip_code,
                'shipping_country'      => $request->country,
            ];
        }

        // Calculate totals
        $subtotal              = $cart->items->sum(
            fn ($i) => ($i->variant?->price ?? $i->product->price) * $i->quantity
        );
        $discount              = 0;
        $freeShippingThreshold = (float) Setting::get('free_shipping_threshold', 100);
        $shippingCost          = (float) Setting::get('default_shipping_cost', 10);

        if ($cart->coupon && $cart->coupon->isValid($subtotal)) {
            $discount = $cart->coupon->calculateDiscount($subtotal);
        }

        $shipping = ($subtotal - $discount) >= $freeShippingThreshold ? 0 : $shippingCost;
        $total    = $subtotal - $discount + $shipping;

        $order = DB::transaction(function () use ($request, $cart, $shippingData, $subtotal, $discount, $shipping, $total) {
            // Create the order
            $order = Order::create(array_merge($shippingData, [
                'user_id'        => auth()->id(),
                'coupon_id'      => $cart->coupon_id,
                'status'         => 'pending',
                'payment_status' => 'pending',
                'payment_method' => $request->payment_method,
                'subtotal'       => $subtotal,
                'discount'       => $discount,
                'shipping'       => $shipping,
                'tax'            => 0,
                'total'          => $total,
                'notes'          => $request->notes,
            ]));

            // Create order items and update stock
            foreach ($cart->items as $cartItem) {
                $price = $cartItem->variant?->price ?? $cartItem->product->price;

                $order->items()->create([
                    'product_id'         => $cartItem->product_id,
                    'product_variant_id' => $cartItem->product_variant_id,
                    'product_name'       => $cartItem->product->name,
                    'variant_name'       => $cartItem->variant?->name,
                    'sku'                => $cartItem->variant?->sku ?? $cartItem->product->sku,
                    'price'              => $price,
                    'quantity'           => $cartItem->quantity,
                    'total'              => $price * $cartItem->quantity,
                ]);

                // Decrement stock
                if ($cartItem->variant) {
                    $cartItem->variant->decrement('stock', $cartItem->quantity);
                } else {
                    $cartItem->product->decrement('stock', $cartItem->quantity);
                }
            }

            // Increment coupon usage
            if ($cart->coupon) {
                $cart->coupon->increment('usage_count');
            }

            // Clear the cart
            $cart->items()->delete();
            $cart->update(['coupon_id' => null]);

            return $order;
        });

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'Order placed successfully!');
    }
}
