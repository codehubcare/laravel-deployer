<?php

use Illuminate\Support\Facades\Route;
use Codehubcare\LaravelDeployer\Http\Controllers\DeployController;

Route::get('laravel-deployer', [DeployController::class, 'index'])->name('laravel-deployer.index');
