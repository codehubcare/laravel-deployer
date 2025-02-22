<?php

namespace CodehubCare\LaravelDeployer\Http\Controllers;

use App\Http\Controllers\Controller;
use Codehubcare\LaravelDeployer\Services\Ssh;
use Illuminate\Support\Str;

class DeployController extends Controller
{

    /**
     * Show the index page
     */
    public function index()
    {

        $changedFiles = [];
        $response = '';

        // List current edited files
        exec('git diff --name-status HEAD origin/main', $changedFiles);

        $changedFiles = collect($changedFiles)->map(function ($file) {
            $file = Str::replace('../', '', $file);
            return Str::trim(Str::substr($file, 2));
        });

    
        $server = $this->connectToServer();
        $response = $server->execute('ls');

        dd($response, $server);

        return view('laravel-deployer::index', compact('changedFiles'));
    }


    /**
     * Set src and public paths
     * 
     */
    private function setServerPaths()
    {
        $server = $this->connectToServer();

        $srcPath = trim($server->execute('cd src && pwd'));
        $publicPath = trim($server->execute('cd public_html && pwd'));

        return [
            $srcPath,
            $publicPath,
        ];
    }

    /**
     * Connect to Server
     */
    private function connectToServer()
    {
        $ssh = new Ssh(config("laravel-deployer.ftp.host"), config('laravel-deployer.ftp.username'), config('laravel-deployer.ftp.password'));
        $ssh->connect();
        return $ssh;
    }


    /**
     * Run deploy
     */
    public function run()
    {
        $ssh = new Ssh(config("laravel-deployer.ftp.host"), config('laravel-deployer.ftp.username'), config('laravel-deployer.ftp.password'));
        $ssh->connect();


        
        $destinationPath = $this->getSrcPath();
        $appFolder = app_path();
        $resourcesFolder = resource_path();
        $routeFolder = base_path('routes');

        $ssh->uploadDirectory($appFolder, $destinationPath . '/app');
        $ssh->uploadDirectory($resourcesFolder, $destinationPath . '/resources');
        $ssh->uploadDirectory($routeFolder, $destinationPath . '/routes');
        
        $ssh->disconnect();
        
        return "done";

    }

    public function getPublicPath($path = '')
    {
        return config('laravel-deployer.public_path') . $path;
    }

    public function getSrcPath($path = '')
    {
        
        return config('laravel-deployer.src_path') . $path;
    }
}
