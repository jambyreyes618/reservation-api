<?php

namespace App\Providers;

use App\Models\Product;
use App\Policies\V1\ProductPolicy;
use Illuminate\Support\Facades\Gate;
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
        // Product models are protected by ProductPolicy
        Gate::policy(Product::class, ProductPolicy::class);
    }
}