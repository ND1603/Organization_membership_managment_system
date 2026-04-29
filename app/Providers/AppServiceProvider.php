<?php

namespace App\Providers;

use App\Http\Responses\RegisterResponse;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Tell Fortify to use our custom RegisterResponse
        // instead of its default one
        $this->app->singleton(
            RegisterResponseContract::class,
            RegisterResponse::class
        );
    }

    public function boot(): void
    {
        //
    }
}