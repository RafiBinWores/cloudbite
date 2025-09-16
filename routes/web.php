<?php

use App\Livewire\Admin\AddOns\AddOns;
use App\Livewire\Admin\Buns\Buns;
use App\Livewire\Admin\Categories\Index;
use App\Livewire\Admin\Coupons\Coupons;
use App\Livewire\Admin\Crusts\Crusts;
use App\Livewire\Admin\Cuisine\Cuisines;
use App\Livewire\Admin\Dishes\CreateDish;
use App\Livewire\Admin\Dishes\Dishes;
use App\Livewire\Admin\Dishes\EditDish;
use App\Livewire\Admin\Dishes\ShowDish;
use App\Livewire\Admin\Tags\Tags;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('admin/dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->prefix('admin')->group(function () {
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
});

require __DIR__ . '/auth.php';
