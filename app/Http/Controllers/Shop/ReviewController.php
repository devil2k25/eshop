<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating'     => 'required|integer|min:1|max:5',
            'title'      => 'required|string|max:255',
            'body'       => 'required|string|max:5000',
        ]);

        $user = auth()->user();

        // Prevent duplicate reviews
        $alreadyReviewed = Review::where('product_id', $request->product_id)
            ->where('user_id', $user->id)
            ->exists();

        if ($alreadyReviewed) {
            return back()->with('error', 'You have already reviewed this product.');
        }

        // Check whether the user has ordered this product (for verified badge)
        $verifiedOrder = Order::where('user_id', $user->id)
            ->whereHas('items', fn ($q) => $q->where('product_id', $request->product_id))
            ->where('status', 'delivered')
            ->first();

        Review::create([
            'product_id'  => $request->product_id,
            'user_id'     => $user->id,
            'order_id'    => $verifiedOrder?->id,
            'rating'      => $request->rating,
            'title'       => $request->title,
            'body'        => $request->body,
            'is_approved' => false,
            'is_verified' => $verifiedOrder !== null,
        ]);

        return back()->with('success', 'Review submitted and pending approval. Thank you!');
    }

    public function destroy(Review $review): RedirectResponse
    {
        abort_unless($review->user_id === auth()->id(), 403);

        $review->delete();

        return back()->with('success', 'Review deleted.');
    }
}
