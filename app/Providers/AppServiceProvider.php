<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\PlantIdentificationService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register PlantIdentificationService as a singleton
        $this->app->singleton(PlantIdentificationService::class, function ($app) {
            return new PlantIdentificationService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
