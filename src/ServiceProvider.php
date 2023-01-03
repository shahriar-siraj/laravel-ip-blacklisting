<?php


namespace ShahriarSiraj\LaravelIpBlacklisting;

use Illuminate\Routing\Router;
use Illuminate\Console\Scheduling\Schedule;
use ShahriarSiraj\LaravelIpBlacklisting\Console\Commands\CleanOutdatedIps;
use ShahriarSiraj\LaravelIpBlacklisting\Middleware\IpBlacklistingMiddleware;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Bootstrap any package services.
     *
     * @param Router $router
     * @return void
     */
    public function boot(Router $router)
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->mergeConfigFrom(
            __DIR__.'/../config/ip_blacklisting.php',
            'ip_blacklisting'
        );

        $this->mergeConfigFrom(
            __DIR__.'/../config/logging.php',
            'logging.channels'
        );

        if ($this->app->runningInConsole()) {
            $this->commands([
                CleanOutdatedIps::class
            ]);
        }

        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            $schedule
                ->command(CleanOutdatedIps::class)
                ->cron(config('ip_blacklisting.cleaner_schedule'));
        });

        $router->pushMiddlewareToGroup('web', IpBlacklistingMiddleware::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
