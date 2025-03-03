<?php

namespace Codehubcare\LaravelDeployer;

use Codehubcare\LaravelDeployer\Commands\DeployCommand;
use Codehubcare\LaravelDeployer\Commands\RunCommand;
use Codehubcare\LaravelDeployer\Commands\SshDetailsCommand;
use Codehubcare\LaravelDeployer\Commands\StorageLinkCommand;
use Illuminate\Support\ServiceProvider;

class LaravelDeployerServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/config/laravel-deployer.php', 'laravel-deployer');
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Load routes and config 
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        $this->mergeConfigFrom(__DIR__ . '/config/laravel-deployer.php', 'laravel-deployer');

        // Optionally publish the config file
        $this->publishes(
            [
                __DIR__ . '/config/laravel-deployer.php' => config_path('laravel-deployer.php'),
            ],
            'laravel-deployer-config',
        );

        // Register the artisan commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                RunCommand::class,
                DeployCommand::class,
                SshDetailsCommand::class,
                StorageLinkCommand::class,
            ]);
        }
    }
}
