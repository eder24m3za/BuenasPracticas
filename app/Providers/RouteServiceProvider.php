<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        try {
            RateLimiter::for('login_attempts', function (Request $request) {
                try {
                    return Limit::perMinute(3)->by($request->user()?->id ?: $request->ip());
                } catch (\PDOException $e) {
                    Log::error('Error al configurar el límite de velocidad para intentos de inicio de sesión: ' . $e->getMessage());
                    return Limit::none();
                }
            });
    
            $this->routes(function () {
                Route::middleware('api')
                    ->prefix('api')
                    ->group(base_path('routes/api.php'));
    
                Route::middleware('web')
                    ->group(base_path('routes/web.php'));
            });
        } catch (\Exception $e) {
            Log::error('Error al configurar el límite de velocidad para intentos de inicio de sesión: ' . $e->getMessage());
        }
    }
}
