<?php

namespace Codehubcare\LaravelDeployer\Commands;

use Codehubcare\LaravelDeployer\Services\Ssh;
use Illuminate\Console\Command;
use Exception;

class DeployCommand extends Command
{
    protected $signature = 'laravel-deployer:deploy';

    protected $description = 'Deploy the application';

    public function handle()
    {
        $this->info('Deploying the application...');

        try {
            // Connect remote server
            $server = $this->connectToServer();

            // Upload App folder
            $server->uploadDirectory(base_path('app') . '/', config('laravel-deployer.src_path') . '/app');
            $this->info('App folder uploaded successfully');

            // Upload Resources folder
            $server->uploadDirectory(resource_path(), config('laravel-deployer.src_path') . '/resources');
            $this->info('Resources folder uploaded successfully');

            // Upload Routes folder
            $server->uploadDirectory(base_path('routes') . '/', config('laravel-deployer.src_path') . '/routes');
            $this->info('Routes folder uploaded successfully');

            // Upload Config folder
            $server->uploadDirectory(base_path('config') . '/', config('laravel-deployer.src_path') . '/config');
            $this->info('Config folder uploaded successfully');

            // Upload Database folder
            $server->uploadDirectory(base_path('database') . '/', config('laravel-deployer.src_path') . '/database');
            $this->info('Database folder uploaded successfully');

            // Run post deployment commands
            $this->runPostDeploymentCommands();

        } catch (Exception $ex) {
            $this->error($ex->getMessage());
            return;
        }

        $server->disconnect();
        $this->info('Application deployed successfully');
    }

    /**
     * Connect to Server
     */
    private function connectToServer()
    {
        $ssh = new Ssh(config('laravel-deployer.ftp.host'), config('laravel-deployer.ftp.username'), config('laravel-deployer.ftp.password'));
        $ssh->connect();
        return $ssh;
    }


    /**
     * Run post deployment commands
     * @return void
     */
    private function runPostDeploymentCommands()
    {
        $this->info('Running post deployment commands...');

        $server = $this->connectToServer();

        $commands = [
            'cd src && php artisan cache:clear',
            'cd src && php artisan config:clear',
            'cd src && php artisan route:clear',
            'cd src && php artisan view:clear',
        ];

        foreach ($commands as $command) {
            $server->execute($command);
        }

        $this->info('Post deployment commands executed successfully');

        $server->disconnect();
    }
}
