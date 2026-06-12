<?php

namespace App\Http\Middleware;

use App\Models\{Cart, Category, Setting, Wishlist};
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        $cartCount = 0;
        $wishlistCount = 0;

        if ($request->user()) {
            $cart = Cart::where('user_id', $request->user()->id)->first();
            $cartCount = $cart ? $cart->items()->sum('quantity') : 0;
            $wishlistCount = Wishlist::where('user_id', $request->user()->id)->count();
        } else {
            $cart = Cart::where('session_id', session()->getId())->first();
            $cartCount = $cart ? $cart->items()->sum('quantity') : 0;
        }

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'is_admin' => $request->user()->is_admin,
                    'avatar' => $request->user()->avatar,
                ] : null,
            ],
            'cartCount' => $cartCount,
            'wishlistCount' => $wishlistCount,
            'flash' => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
            ],
            'appName' => config('app.name'),
            'storeName' => Setting::get('store_name', config('app.name')),
            'currencySymbol' => Setting::get('currency_symbol', '$'),
            'navCategories' => Category::where('is_active', true)->whereNull('parent_id')->orderBy('sort_order')->get(['id', 'name', 'slug']),
        ];
    }
}
