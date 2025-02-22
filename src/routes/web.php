<?php

use Illuminate\Support\Facades\Route;
use Codehubcare\LaravelDeployer\Http\Controllers\DeployController;

Route::get('laravel-deployer', [DeployController::class, 'run'])->name('laravel-deployer.index');
