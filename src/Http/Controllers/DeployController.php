<?php

namespace CodehubCare\LaravelDeployer\Http\Controllers;

use App\Http\Controllers\Controller;
use Codehubcare\LaravelDeployer\Services\Ssh;

class DeployController extends Controller
{

    /**
     * Run deploy
     */
    public function run()
    {
        $ssh = new Ssh('jaheedtrading.com', 'jaheeuwx', 'Admin##12332');
        $ssh->connect();
    
        $serverPath = $this->getSrcPath('config');
        $localPath = config_path();
        
        dd($ssh->downloadDirectory($serverPath, $localPath));
        // dd($ssh->execute('cd src && pwd'));

    }

    public function getPublicPath($path = '')
    {
        return config('laravel-deployer.public_path') . $path;
    }

    public function getSrcPath($path = '')
    {
        
        return "/home1/jaheeuwx/src/" . $path;
        // return config('laravel-deployer.src_path') . $path;
    }
}
