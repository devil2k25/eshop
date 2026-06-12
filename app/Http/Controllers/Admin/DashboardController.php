<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $stats = [
            'total_revenue'      => Order::where('payment_status', 'paid')->sum('total'),
            'total_orders'       => Order::count(),
            'total_products'     => Product::count(),
            'total_customers'    => User::where('is_admin', false)->count(),
            'pending_orders'     => Order::where('status', 'pending')->count(),
            'low_stock_products' => Product::whereColumn('stock', '<=', 'low_stock_threshold')->count(),
            'pending_reviews'    => Review::where('is_approved', false)->count(),
        ];

        $recentOrders = Order::with('user')
            ->latest()
            ->take(10)
            ->get()
            ->map(fn($o) => [
                'id'             => $o->id,
                'order_number'   => $o->order_number,
                'customer'       => $o->user?->name ?? 'Guest',
                'status'         => $o->status,
                'payment_status' => $o->payment_status,
                'total'          => $o->total,
                'created_at'     => $o->created_at->format('M d, Y'),
            ]);

        // Monthly revenue for last 6 months
        $monthlyRevenue = [];
        for ($i = 5; $i >= 0; $i--) {
            $date             = now()->subMonths($i);
            $monthlyRevenue[] = [
                'month'   => $date->format('M Y'),
                'revenue' => Order::where('payment_status', 'paid')
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->sum('total'),
            ];
        }

        $topProducts = Product::withCount('orderItems')
            ->orderByDesc('order_items_count')
            ->take(5)
            ->get(['id', 'name', 'price', 'stock']);

        return Inertia::render('Admin/Dashboard', compact(
            'stats',
            'recentOrders',
            'monthlyRevenue',
            'topProducts'
        ));
    }
}
