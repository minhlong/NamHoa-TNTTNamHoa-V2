<?php

namespace Fireapps\Core;

use Fireapps\Core\Middleware\AppAuthenticate;
use Fireapps\Core\Middleware\SocialAuthRedirect;
use Illuminate\Support\ServiceProvider;

class FireappsProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Define helpers
        foreach (glob(__DIR__.'/../helpers/*.php') as $file) {
            require_once($file);
        }
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        app()->make('router')->aliasMiddleware('fireapps.redirect', SocialAuthRedirect::class);
        app()->make('router')->aliasMiddleware('fireapps.auth', AppAuthenticate::class);

        $this->publishes([
            __DIR__.'/../config/fireapps.php' => config_path('fireapps.php'),
            __DIR__.'/../lang' => base_path('resources/lang'),
        ]);
        $this->loadMigrationsFrom(__DIR__.'/../migrations');
    }
}
