<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Order::with('user');

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->payment_status) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->latest()->paginate(20)->withQueryString();

        return Inertia::render('Admin/Orders/Index', [
            'orders'  => $orders,
            'filters' => $request->only([
                'search',
                'status',
                'payment_status',
                'date_from',
                'date_to',
            ]),
            'statusOptions'        => ['pending', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded'],
            'paymentStatusOptions' => ['pending', 'paid', 'failed', 'refunded'],
        ]);
    }

    public function show(Order $order): Response
    {
        $order->load([
            'user',
            'items.product.primaryImage',
            'coupon',
        ]);

        return Inertia::render('Admin/Orders/Show', [
            'order' => $order,
        ]);
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status'           => ['nullable', Rule::in(['pending', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded'])],
            'payment_status'   => ['nullable', Rule::in(['pending', 'paid', 'failed', 'refunded'])],
            'tracking_number'  => 'nullable|string|max:255',
            'notes'            => 'nullable|string',
        ]);

        $order->update(array_filter($validated, fn($v) => $v !== null));

        // Set shipped_at / delivered_at timestamps automatically
        if (isset($validated['status'])) {
            if ($validated['status'] === 'shipped' && !$order->shipped_at) {
                $order->update(['shipped_at' => now()]);
            }
            if ($validated['status'] === 'delivered' && !$order->delivered_at) {
                $order->update(['delivered_at' => now()]);
            }
        }

        return back()->with('success', 'Order updated successfully.');
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['pending', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded'])],
        ]);

        $order->update(['status' => $validated['status']]);

        if ($validated['status'] === 'shipped' && !$order->shipped_at) {
            $order->update(['shipped_at' => now()]);
        }

        if ($validated['status'] === 'delivered' && !$order->delivered_at) {
            $order->update(['delivered_at' => now()]);
        }

        return back()->with('success', 'Order status updated.');
    }
}
