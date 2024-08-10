<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\JwtService;
use App\Services\Impl\JwtServiceImpl;
use App\Services\LoginService;
use App\Services\Impl\LoginServiceImpl;
use App\Services\UserService;
use App\Services\Impl\UserServiceImpl;
use App\Services\NotificationService;
use App\Services\Impl\NotificationServiceImpl;
use App\Services\DashboardService;
use App\Services\Impl\DashboardServiceImpl;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // jwt
        $this->app->singleton(JwtService::class,JwtServiceImpl::class);
        // login
        $this->app->singleton(LoginService::class, LoginServiceImpl::class);
        // Notification
        $this->app->singleton(NotificationService::class, NotificationServiceImpl::class);
        // Dashboard
        $this->app->singleton(DashboardService::class, DashboardServiceImpl::class);
        // user master
        $this->app->singleton(UserService::class, UserServiceImpl::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
    }
}
