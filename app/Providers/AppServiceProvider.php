<?php

namespace App\Providers;

use App\Services\FaceRecognitionService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register FaceRecognitionService as singleton
        $this->app->singleton(FaceRecognitionService::class, function ($app) {
            return new FaceRecognitionService();
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
