<?php

namespace App\Providers;

use App\Listeners\SetUserOffline;
use App\Listeners\SetUserOnline;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    protected $listen = [
        Login::class => [
            SetUserOnline::class,
        ],
        Logout::class => [
            SetUserOffline::class,
        ],
    ];
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
        //
    }
}
