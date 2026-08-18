<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
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
        $this->configureRateLimiters();
    }

    /**
     * Registra los rate limiters nombrados según config/rate_limits.php.
     */
    private function configureRateLimiters(): void
    {
        foreach (config('rate_limits', []) as $name => $config) {
            RateLimiter::for($name, fn (Request $request) => $this->resolverLimiter($name, $config, $request));
        }
    }

    /**
     * Resuelve el límite para la categoría indicada. La gestión de citas aplica un
     * peso menor y una clave separada cuando la petición genera un reporte (export).
     */
    private function resolverLimiter(string $name, array $config, Request $request): Limit
    {
        $export = $request->has('export_excel') || $request->has('export_pdf');

        if ($name === 'gestion_citas' && $export) {
            $config = config('rate_limits.exportaciones');
        }

        return Limit::perMinutes($config['decay'], $config['attempts'])
            ->by(($name === 'gestion_citas' && $export ? 'export:' : '') . ($request->user()?->id ?? $request->ip()));
    }
}
