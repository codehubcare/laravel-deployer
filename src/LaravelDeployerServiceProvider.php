<?php

namespace Codehubcare\LaravelDeployer;

use Codehubcare\LaravelDeployer\Commands\DeployCommand;
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
        // Load routes, views, migrations, and config
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'laravel-deployer');
        $this->mergeConfigFrom(__DIR__ . '/config/laravel-deployer.php', 'laravel-deployer');

        // Optionally publish views and config
        $this->publishes(
            [
                __DIR__ . '/resources/views' => resource_path('views/vendor/laravel-deployer'),
            ],
            'laravel-deployer-views',
        );

        $this->publishes(
            [
                __DIR__ . '/config/laravel-deployer.php' => config_path('laravel-deployer.php'),
            ],
            'laravel-deployer-config',
        );

        // Register the artisan commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                DeployCommand::class,
                SshDetailsCommand::class,
                StorageLinkCommand::class,
            ]);
        }
    }
}
