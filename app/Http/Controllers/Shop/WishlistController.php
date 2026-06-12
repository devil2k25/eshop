<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class WishlistController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(): Response
    {
        $wishlistItems = auth()->user()
            ->wishlist()
            ->with(['product.primaryImage', 'product.category'])
            ->latest()
            ->get()
            ->map(fn ($item) => [
                'id'      => $item->id,
                'product' => [
                    'id'            => $item->product->id,
                    'name'          => $item->product->name,
                    'slug'          => $item->product->slug,
                    'price'         => $item->product->price,
                    'compare_price' => $item->product->compare_price,
                    'avg_rating'    => $item->product->avg_rating,
                    'review_count'  => $item->product->review_count,
                    'is_new'        => $item->product->is_new,
                    'stock'         => $item->product->stock,
                    'image'         => $item->product->primaryImage?->image,
                    'category'      => $item->product->category,
                ],
            ]);

        return Inertia::render('Shop/Wishlist', compact('wishlistItems'));
    }

    public function toggle(Product $product): RedirectResponse
    {
        $user     = auth()->user();
        $existing = $user->wishlist()->where('product_id', $product->id)->first();

        if ($existing) {
            $existing->delete();

            return back()->with('success', 'Removed from wishlist.');
        }

        $user->wishlist()->create(['product_id' => $product->id]);

        return back()->with('success', 'Added to wishlist.');
    }
}
