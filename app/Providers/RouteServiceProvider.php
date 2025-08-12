<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use App\Models\Conference;
use App\Models\Audience;

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
            return is_numeric($value)
                ? Audience::findOrFail($value)
                : Audience::where('public_id', $value)->firstOrFail();
        });
    }
}
