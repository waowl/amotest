<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use AmoCRM\Client\AmoCRMApiClient;
use Symfony\Component\Dotenv\Dotenv;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
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
