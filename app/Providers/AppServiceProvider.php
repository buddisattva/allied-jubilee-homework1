<?php

namespace App\Providers;

use App\Services\User\BasicLoginService;
use App\Services\User\BasicRegisterService;
use App\Services\User\Contracts\LoginService;
use App\Services\User\Contracts\RegisterService;
use Facebook\Facebook;
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

        // Facebook
        $this->app->singleton(Facebook::class, function () {
            return new Facebook([
                'app_id' => config('facebook.app_id'),
                'app_secret' => config('facebook.app_secret'),
                'default_graph_version' => config('facebook.default_graph_version'),
            ]);
        });
    }
}
