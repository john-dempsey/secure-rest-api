<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Event;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Supplier;
use App\Models\Product;
use App\Policies\CustomerPolicy;
use App\Policies\OrderPolicy;
use App\Policies\SupplierPolicy;
use App\Policies\ProductPolicy;

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
        Gate::policy(Customer::class, CustomerPolicy::class);
        Gate::policy(Order::class, OrderPolicy::class);
        Gate::policy(Supplier::class, SupplierPolicy::class);
        Gate::policy(Product::class, ProductPolicy::class);
    }
}
