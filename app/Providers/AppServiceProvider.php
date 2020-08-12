<?php

namespace App\Providers;

use App\Services\User\BasicLoginService;
use App\Services\User\BasicRegisterService;
use App\Services\User\Contracts\LoginService;
use App\Services\User\Contracts\RegisterService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(LoginService::class, BasicLoginService::class);
        $this->app->bind(RegisterService::class, BasicRegisterService::class);
    }
}
