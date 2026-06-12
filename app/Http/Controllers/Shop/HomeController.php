<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(): Response
    {
        $banners = Banner::where('is_active', true)
            ->where('position', 'home')
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', now()))
            ->orderBy('sort_order')
            ->get();

        $featuredProducts = Product::active()
            ->featured()
            ->with(['primaryImage', 'category'])
            ->take(8)
            ->get()
            ->map(fn ($p) => $p->only([
                'id', 'name', 'slug', 'price', 'compare_price',
                'avg_rating', 'review_count', 'is_new', 'primaryImage', 'category',
            ]));

        $newArrivals = Product::active()
            ->new()
            ->with(['primaryImage', 'category'])
            ->latest()
            ->take(8)
            ->get()
            ->map(fn ($p) => $p->only([
                'id', 'name', 'slug', 'price', 'compare_price',
                'avg_rating', 'review_count', 'is_new', 'primaryImage', 'category',
            ]));

        $categories = Category::where('is_active', true)
            ->whereNull('parent_id')
            ->withCount('products')
            ->orderBy('sort_order')
            ->take(8)
            ->get();

        $bestSellers = Product::active()
            ->withCount('orderItems')
            ->orderByDesc('order_items_count')
            ->with(['primaryImage', 'category'])
            ->take(8)
            ->get()
            ->map(fn ($p) => $p->only([
                'id', 'name', 'slug', 'price', 'compare_price',
                'avg_rating', 'review_count', 'is_new', 'primaryImage', 'category',
            ]));

        return Inertia::render('Shop/Home', compact(
            'banners',
            'featuredProducts',
            'newArrivals',
            'categories',
            'bestSellers'
        ));
    }
}
