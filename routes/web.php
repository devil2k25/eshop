<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Shop;
use App\Http\Controllers\Admin;

// ─── Storefront Routes ───────────────────────────────────────────────────────

Route::get('/', [Shop\HomeController::class, 'index'])->name('home');

// Products
Route::get('/products', [Shop\ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [Shop\ProductController::class, 'show'])->name('products.show');

// Cart (guest + auth)
Route::get('/cart', [Shop\CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [Shop\CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/items/{item}', [Shop\CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/items/{item}', [Shop\CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/coupon', [Shop\CartController::class, 'applyCoupon'])->name('cart.coupon.apply');
Route::delete('/cart/coupon', [Shop\CartController::class, 'removeCoupon'])->name('cart.coupon.remove');

// Auth-protected storefront routes
Route::middleware(['auth'])->group(function () {
    // Checkout
    Route::get('/checkout', [Shop\CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [Shop\CheckoutController::class, 'store'])->name('checkout.store');

    // Orders
    Route::get('/orders', [Shop\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [Shop\OrderController::class, 'show'])->name('orders.show');

    // Wishlist
    Route::get('/wishlist', [Shop\WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/toggle/{product}', [Shop\WishlistController::class, 'toggle'])->name('wishlist.toggle');

    // Reviews
    Route::post('/reviews', [Shop\ReviewController::class, 'store'])->name('reviews.store');
    Route::delete('/reviews/{review}', [Shop\ReviewController::class, 'destroy'])->name('reviews.destroy');

    // Profile
    Route::get('/profile', [Shop\ProfileController::class, 'index'])->name('profile.index');
    Route::patch('/profile', [Shop\ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::patch('/profile/password', [Shop\ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::get('/profile/addresses', [Shop\ProfileController::class, 'addresses'])->name('profile.addresses');
    Route::post('/profile/addresses', [Shop\ProfileController::class, 'storeAddress'])->name('profile.addresses.store');
    Route::patch('/profile/addresses/{address}', [Shop\ProfileController::class, 'updateAddress'])->name('profile.addresses.update');
    Route::delete('/profile/addresses/{address}', [Shop\ProfileController::class, 'deleteAddress'])->name('profile.addresses.delete');
    Route::patch('/profile/addresses/{address}/default', [Shop\ProfileController::class, 'setDefaultAddress'])->name('profile.addresses.default');
});

// ─── Admin Routes ────────────────────────────────────────────────────────────

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // Products
    Route::resource('products', Admin\ProductController::class);
    Route::post('products/{id}/restore', [Admin\ProductController::class, 'restore'])->name('products.restore');
    Route::delete('products/images/{image}', [Admin\ProductController::class, 'deleteImage'])->name('products.images.delete');
    Route::patch('products/images/{image}/primary', [Admin\ProductController::class, 'setPrimaryImage'])->name('products.images.primary');

    // Categories, Brands, Coupons, Banners
    Route::resource('categories', Admin\CategoryController::class);
    Route::resource('brands', Admin\BrandController::class);
    Route::resource('coupons', Admin\CouponController::class);
    Route::resource('banners', Admin\BannerController::class);

    // Orders
    Route::resource('orders', Admin\OrderController::class)->only(['index', 'show', 'update']);
    Route::patch('orders/{order}/status', [Admin\OrderController::class, 'updateStatus'])->name('orders.status');

    // Customers
    Route::resource('customers', Admin\CustomerController::class)->only(['index', 'show', 'update']);
    Route::patch('customers/{customer}/toggle-active', [Admin\CustomerController::class, 'toggleActive'])->name('customers.toggle-active');

    // Reviews
    Route::resource('reviews', Admin\ReviewController::class)->only(['index', 'destroy']);
    Route::patch('reviews/{review}/approve', [Admin\ReviewController::class, 'approve'])->name('reviews.approve');

    // Settings
    Route::get('settings', [Admin\SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [Admin\SettingController::class, 'update'])->name('settings.update');

    // Reports
    Route::get('reports', [Admin\ReportController::class, 'index'])->name('reports.index');
});

// ─── Auth Routes ─────────────────────────────────────────────────────────────

require __DIR__.'/auth.php';
