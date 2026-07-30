<?php

namespace App\Providers;

use App\Models\ServiceOrder;
use App\Observers\ServiceOrderObserver;
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
        ServiceOrder::observe(ServiceOrderObserver::class);
    }
}
