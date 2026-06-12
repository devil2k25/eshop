<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Product::active()->with(['primaryImage', 'category', 'brand']);

        // Search
        if ($request->search) {
            $search = $request->search;
            $query->where(fn ($q) => $q
                ->where('name', 'like', "%$search%")
                ->orWhere('description', 'like', "%$search%")
            );
        }

        // Category filter
        if ($request->category) {
            $category = Category::where('slug', $request->category)->first();
            if ($category) {
                $childIds = $category->children()->pluck('id');
                $query->where(fn ($q) => $q
                    ->where('category_id', $category->id)
                    ->orWhereIn('category_id', $childIds)
                );
            }
        }

        // Brand filter
        if ($request->brand) {
            $brand = Brand::where('slug', $request->brand)->first();
            if ($brand) {
                $query->where('brand_id', $brand->id);
            }
        }

        // Tag filter
        if ($request->tag) {
            $query->whereHas('tags', fn ($q) => $q->where('slug', $request->tag));
        }

        // Price range
        if ($request->min_price) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->max_price) {
            $query->where('price', '<=', $request->max_price);
        }

        // Rating filter
        if ($request->min_rating) {
            $query->where('avg_rating', '>=', $request->min_rating);
        }

        // In stock filter
        if ($request->in_stock) {
            $query->where('stock', '>', 0);
        }

        // Sorting
        switch ($request->sort) {
            case 'price_asc':
                $query->orderBy('price');
                break;
            case 'price_desc':
                $query->orderByDesc('price');
                break;
            case 'newest':
                $query->latest();
                break;
            case 'rating':
                $query->orderByDesc('avg_rating');
                break;
            case 'popular':
                $query->orderByDesc('views');
                break;
            default:
                $query->orderByDesc('is_featured')->latest();
        }

        $products = $query->paginate(24)->withQueryString();

        $priceRange = Product::active()
            ->selectRaw('MIN(price) as min, MAX(price) as max')
            ->first();

        return Inertia::render('Shop/Products/Index', [
            'products'   => $products,
            'categories' => Category::where('is_active', true)
                ->whereNull('parent_id')
                ->with('children')
                ->withCount('products')
                ->orderBy('sort_order')
                ->get(),
            'brands' => Brand::where('is_active', true)
                ->withCount('products')
                ->orderBy('name')
                ->get(['id', 'name', 'slug', 'products_count']),
            'tags'       => Tag::withCount('products')->orderByDesc('products_count')->take(20)->get(),
            'priceRange' => $priceRange,
            'filters'    => $request->only([
                'search', 'category', 'brand', 'tag',
                'min_price', 'max_price', 'min_rating', 'in_stock', 'sort',
            ]),
        ]);
    }

    public function show(string $slug): Response
    {
        $product = Product::where('slug', $slug)
            ->active()
            ->with([
                'images',
                'variants',
                'tags',
                'category',
                'brand',
                'reviews' => fn ($q) => $q->approved()->with('user')->latest()->take(10),
            ])
            ->firstOrFail();

        $product->increment('views');

        $related = Product::active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with(['primaryImage'])
            ->take(4)
            ->get();

        $isWishlisted = auth()->check()
            ? auth()->user()->wishlist()->where('product_id', $product->id)->exists()
            : false;

        return Inertia::render('Shop/Products/Show', compact('product', 'related', 'isWishlisted'));
    }
}
