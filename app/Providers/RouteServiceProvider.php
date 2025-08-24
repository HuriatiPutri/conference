<?php

namespace App\Providers;

use App\Models\Audience;
use App\Models\Conference;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, etc.
     */
    public function boot(): void
    {
        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });

        Route::bind('conference', function ($value) {
            return Conference::where('public_id', $value)->firstOrFail();
        });

        Route::bind('audience', function ($value) {
            return Audience::where('public_id', $value)->firstOrFail();
        });
    }
}
