<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\CompanyInfo;
use App\Models\Order;
use App\Observers\CategoryObserver;
use App\Observers\CompanyInfoObserver;
use App\Observers\OrderObserver;
use App\Repositories\CartRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // For categories
        View::share('navbarCategories', Cache::remember('navbar_categories_v1', 3600, function () {
            return Category::query()
                ->where('status', true)
                ->orderBy('created_at', 'desc')
                ->get(['id', 'name', 'slug', 'image']);
        }));

        View::share('businessSetting', Cache::remember('business_settings_v1', 3600, function () {
            return CompanyInfo::query()->first();
        }));

        // Cart item count (per request, per user/session)
        View::composer('*', function ($view) {
            $repo = app(CartRepository::class);

            $cart = $repo->loadCartNullable(['items']);

            // Sum of quantities; change to ->count() if you prefer line count
            $count = $cart ? (int) $cart->items->sum('qty') : 0;

            $view->with('cartItemCount', $count);
        });

        Category::observe(CategoryObserver::class);
        CompanyInfo::observe(CompanyInfoObserver::class);

        Order::observe(OrderObserver::class);
    }
}
