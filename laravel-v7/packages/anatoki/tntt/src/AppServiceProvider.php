<?php

namespace TNTT;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use TNTT\Exceptions\CustomHandler;
use TNTT\Middleware\CheckOwner;
use TNTT\Middleware\CheckTeacher;
use TNTT\Repositories\TaiKhoanRepository;

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
        $this->initACL();

        $this->app->singleton(TaiKhoanRepository::class);

        // Custom handle error
        $this->app->bind(ExceptionHandler::class, CustomHandler::class);

        // Load Routers
        $this->loadRoutesFrom(__DIR__.'/../routers.php');

        // Force update config file
        $this->publishes([
            __DIR__.'/../config' => base_path('config'),
            __DIR__.'/../lang'   => resource_path('lang/en'),
        ]);
    }

    protected function initACL()
    {
        // Validate supper admin
        Gate::before(function ($user, $ability) {
            return $user->isSuperAdmin() ? true : null;
        });

        Route::aliasMiddleware('isOwner', CheckOwner::class);
        Route::aliasMiddleware('isTeacher', CheckTeacher::class);
    }
}
