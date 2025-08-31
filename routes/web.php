<?php

use App\Livewire\Admin\Categories\Index;
use App\Livewire\Admin\Crusts\Crusts;
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


    Route::get('categories', Index::class)->name('categories.index');

    Route::get('crusts', Crusts::class)->name('crusts.index');

});

require __DIR__.'/auth.php';
