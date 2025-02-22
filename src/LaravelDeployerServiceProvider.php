<?php

namespace Codehubcare\LaravelDeployer;

use Illuminate\Support\ServiceProvider;

class LaravelDeployerServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {}

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Load the routes
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');

        // Publish the configuration file
        $this->publishes([
            __DIR__ . '/../config/laravel-deployer.php' => config_path('laravel-deployer.php'),
        ]);

        // Load the views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'laravel-deployer');

    }
}
