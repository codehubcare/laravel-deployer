<?php

namespace Codehubcare\LaravelDeployer;

use Illuminate\Support\ServiceProvider;

class LaravelDeployerServiceProvider extends ServiceProvider
{
    public function register()
    {

        // load routes
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
    }
}
