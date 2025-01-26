<?php

namespace Codehubcare\LaravelDeployer;

use Illuminate\Support\ServiceProvider;

class LaravelDeployerServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/laravel-deployer.php',
            'laravel-deployer'
        );

        // load routes
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/laravel-deployer.php' => config_path('laravel-deployer.php'),
        ], 'config');
    }
}
