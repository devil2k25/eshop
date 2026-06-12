<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(): Response
    {
        $orders = auth()->user()
            ->orders()
            ->withCount('items')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Shop/Orders/Index', compact('orders'));
    }

    public function show(Order $order): Response
    {
        // Ensure the order belongs to the authenticated user
        abort_unless($order->user_id === auth()->id(), 403);

        $order->load([
            'items.product.primaryImage',
            'items.variant',
            'coupon',
        ]);

        return Inertia::render('Shop/Orders/Show', compact('order'));
    }
}
