<?php

namespace TNTT;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use TNTT\Exceptions\CustomHandler;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Custom handle error
        $this->app->bind(ExceptionHandler::class, CustomHandler::class);

        // Load Routers
        $this->loadRoutesFrom(__DIR__.'/../routers.php');

        // Force update config file
        $this->publishes([
            __DIR__.'/../config' => base_path('config'),
            __DIR__.'/../lang'   => resource_path('lang/en'),
        ]);

        // Validate supper admin
        Gate::before(function ($user, $ability) {
            return $user->isSuperAdmin() ? true : null;
        });
    }
}
