<?php

namespace TNTT;

use Illuminate\Contracts\Debug\ExceptionHandler;
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
        $this->loadRoutesFrom(__DIR__.'/../routers.php');
        // app()->make('router')->aliasMiddleware('fireapps.redirect', SocialAuthRedirect::class);
        $this->publishes([
            __DIR__.'/../config' => base_path('config'),
        ]);

        $this->app->bind(
            ExceptionHandler::class,
            CustomHandler::class
        );
    }
}
