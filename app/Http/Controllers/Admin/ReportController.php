<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ReportController extends Controller
{
    public function index(): Response
    {
        // ── Revenue by month (last 12 months) ────────────────────────────────
        $revenueByMonth = [];
        for ($i = 11; $i >= 0; $i--) {
            $date             = now()->subMonths($i);
            $revenueByMonth[] = [
                'month'   => $date->format('M Y'),
                'revenue' => Order::where('payment_status', 'paid')
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->sum('total'),
                'orders'  => Order::where('payment_status', 'paid')
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
            ];
        }

        // ── Top selling products (last 30 days) ──────────────────────────────
        $topProducts = OrderItem::select(
                'product_id',
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(total) as total_revenue')
            )
            ->with('product:id,name,price,stock,slug')
            ->whereHas('order', fn($q) => $q
                ->where('payment_status', 'paid')
                ->where('created_at', '>=', now()->subDays(30))
            )
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->take(10)
            ->get()
            ->map(fn($item) => [
                'product_id'     => $item->product_id,
                'name'           => $item->product?->name ?? 'Unknown',
                'price'          => $item->product?->price,
                'stock'          => $item->product?->stock,
                'total_quantity' => $item->total_quantity,
                'total_revenue'  => $item->total_revenue,
            ]);

        // ── Orders by status ─────────────────────────────────────────────────
        $ordersByStatus = Order::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get()
            ->mapWithKeys(fn($row) => [$row->status => $row->count]);

        // Ensure all statuses are represented even if count is zero
        $allStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded'];
        foreach ($allStatuses as $status) {
            if (!isset($ordersByStatus[$status])) {
                $ordersByStatus[$status] = 0;
            }
        }

        // ── Revenue by category ──────────────────────────────────────────────
        $revenueByCategory = Category::select('categories.id', 'categories.name')
            ->join('products', 'products.category_id', '=', 'categories.id')
            ->join('order_items', 'order_items.product_id', '=', 'products.id')
            ->join('orders', function ($join) {
                $join->on('orders.id', '=', 'order_items.order_id')
                     ->where('orders.payment_status', '=', 'paid');
            })
            ->select(
                'categories.id',
                'categories.name',
                DB::raw('SUM(order_items.total) as total_revenue'),
                DB::raw('SUM(order_items.quantity) as total_quantity')
            )
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_revenue')
            ->get();

        // ── Summary stats ────────────────────────────────────────────────────
        $summary = [
            'total_revenue'   => Order::where('payment_status', 'paid')->sum('total'),
            'total_orders'    => Order::count(),
            'average_order'   => Order::where('payment_status', 'paid')->avg('total') ?? 0,
            'total_customers' => \App\Models\User::where('is_admin', false)->count(),
        ];

        return Inertia::render('Admin/Reports/Index', [
            'revenueByMonth'    => $revenueByMonth,
            'topProducts'       => $topProducts,
            'ordersByStatus'    => $ordersByStatus,
            'revenueByCategory' => $revenueByCategory,
            'summary'           => $summary,
        ]);
    }
}
