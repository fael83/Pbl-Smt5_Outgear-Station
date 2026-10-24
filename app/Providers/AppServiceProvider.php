<?php

namespace App\Providers;

use App\Auth\CachedEloquentUserProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
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
        // Cache data pengguna login agar tidak query `users` di setiap request.
        Auth::provider('cached-eloquent', fn ($app, array $config) => new CachedEloquentUserProvider(
            $app['hash'],
            $config['model'],
        ));

        Blade::anonymousComponentPath(resource_path('views/komponen'), 'komponen');
    }
}
