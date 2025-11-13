<?php

namespace App\Providers;

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
        // Allow overriding the public path so assets resolve correctly when
        // the web root is not the framework's /public directory (e.g. shared hosting public_html)
        $customPublic = env('APP_PUBLIC_PATH');
        if (!empty($customPublic)) {
            $this->app->bind('path.public', fn () => rtrim($customPublic, "/ "));
        }
    }
}
