<?php

use App\Http\Controllers\OrderThankYouController;
use App\Http\Controllers\SslCommerzController;
use App\Livewire\Admin\AddOns\AddOns;
use App\Livewire\Admin\Banners\Banners;
use App\Livewire\Admin\Buns\Buns;
use App\Livewire\Admin\Categories\Index;
use App\Livewire\Admin\Coupons\Coupons;
use App\Livewire\Admin\Crusts\Crusts;
use App\Livewire\Admin\Cuisine\Cuisines;
use App\Livewire\Admin\Delivery\CreateDeliveryMan;
use App\Livewire\Admin\Delivery\Delivery;
use App\Livewire\Admin\Dishes\CreateDish;
use App\Livewire\Admin\Dishes\Dishes;
use App\Livewire\Admin\Dishes\EditDish;
use App\Livewire\Admin\Dishes\ShowDish;
use App\Livewire\Admin\Tags\Tags;
use App\Livewire\Frontend\Account\Account;
use App\Livewire\Frontend\Cart\CartPage;
use App\Livewire\Frontend\Checkout\CheckoutPage;
use App\Livewire\Frontend\Home;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

Route::get('/', Home::class)->name('home');

// cart page
Route::get('/cart', CartPage::class)->name('cart.page');

Route::middleware(['auth', 'role:user,manager,admin'])->group(function () {
    Route::get('checkout', CheckoutPage::class)->name('checkout');
    Route::get('/order/thank-you/{code}', OrderThankYouController::class)->name('orders.thankyou');

    // SSl Payment routes
    Route::get('/payment/ssl/init/{order}', [SslCommerzController::class, 'init'])->name('ssl.init');
    Route::post('/payment/ssl/success', [SslCommerzController::class, 'success'])->name('ssl.success')->withoutMiddleware([VerifyCsrfToken::class]);
    Route::post('/payment/ssl/fail', [SslCommerzController::class, 'fail'])->name('ssl.fail')->withoutMiddleware([VerifyCsrfToken::class]);
    Route::post('/payment/ssl/cancel', [SslCommerzController::class, 'cancel'])->name('ssl.cancel')->withoutMiddleware([VerifyCsrfToken::class]);

    // IPN (server-to-server) — optional but recommended
    Route::post('/payment/ssl/ipn', [SslCommerzController::class, 'ipn'])->name('ssl.ipn')->withoutMiddleware([VerifyCsrfToken::class]);

    // Account Routes
    Route::get('account', Account::class)->name('account');
    Route::get('account/profile', Profile::class)->name('account.profile');
});

// Admin Dashboard Route Starts
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::view('dashboard', 'dashboard')
        ->name('dashboard');

    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');

    // Categories Route
    Route::get('categories', Index::class)->name('categories.index');

    // Crust Routes
    Route::get('crusts', Crusts::class)->name('crusts.index');

    // Bun Route
    Route::get('buns', Buns::class)->name('buns.index');

    // Add Ons Route
    Route::get('add-ons', AddOns::class)->name('addOns.index');

    // Cuisine Route
    Route::get('cuisine', Cuisines::class)->name('cuisines.index');

    // Tags Route
    Route::get('tags', Tags::class)->name('tags.index');

    // Dishes Route
    Route::get('dishes', Dishes::class)->name('dishes.index');
    Route::get('dishes/create', CreateDish::class)->name('dishes.create');
    Route::get('dishes/{slug}', ShowDish::class)->name('dishes.show');
    Route::get('dishes/{dish}/edit', EditDish::class)->name('dishes.edit');

    // Coupon Route
    Route::get('coupons', Coupons::class)->name('coupons.index');

    // Banner Route
    Route::get('banners', Banners::class)->name('banners.index');

    // Delivery Man route
    Route::get('delivery', Delivery::class)->name('delivery.index');
    Route::get('delivery/create', CreateDeliveryMan::class)->name('delivery.create');
});

// Auth Routes
require __DIR__ . '/auth.php';
