<?php

use Illuminate\Support\Facades\Route;
use Codehubcare\LaravelDeployer\Http\Controllers\DeployController;

Route::get('deploy', [DeployController::class, 'run']);
