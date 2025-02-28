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

            // Log user about uploading directories
            $this->info('Uploading directories...');

            // Get user  input for directories to exclude and then merge it to the default excluded directories
            $excludedDirectories = $this->ask('Enter directories to exclude separated by comma (vendor, node_modules, storage)');
            $excludedDirectories = explode(',', $excludedDirectories);
            $excludedDirectories = array_map('trim', $excludedDirectories);
            $excludedDirectories = array_merge($excludedDirectories, ['vendor', 'node_modules', 'storage']);
            $excludedDirectories = array_unique($excludedDirectories);

            // display excluded directories
            $this->info('Excluded directories: ' . implode(', ', $excludedDirectories));

            $this->info('---------------------------------');


            // Upload all directories from the Laravel root path to the remote server (excluding specified directories)
            $directories = glob(base_path() . '/*', GLOB_ONLYDIR);
            foreach ($directories as $directory) {

                // Skip excluded directories
                if(in_array(basename($directory), $excludedDirectories)) {
                    continue;
                }

                $this->info('Uploading ' . basename($directory) . '...');

                $server->uploadDirectory($directory, config('laravel-deployer.src_path') . '/' . basename($directory));
                $this->info(basename($directory) . ' uploaded successfully to: ' . config('laravel-deployer.src_path') . '/' . basename($directory));
            }

            // Upload all files from the Laravel root path to the remote server except .env file
            $files = glob(base_path() . '/*');
            foreach ($files as $file) {

                // Skip .env file
                if (basename($file) == '.env') {
                    continue;
                }

                if (is_dir($file)) {
                    continue;
                }

                $this->info('Uploading ' . basename($file) . '...');

                $server->upload($file, config('laravel-deployer.src_path') . '/' . basename($file));
                $this->info(basename($file) . ' uploaded successfully to: ' . config('laravel-deployer.src_path') . '/' . basename($file));
            }


            // Upload all files and directors of the public directory to the remote server except index.php file
            $publicFiles = glob(public_path() . '/*');
            foreach ($publicFiles as $file) {

                // Skip index.php file
                if (basename($file) == 'index.php') {
                    continue;
                }

                $this->info('Uploading ' . basename($file) . '...');
                $server->upload($file, config('laravel-deployer.public_path') . '/public/' . basename($file));
                $this->info(basename($file) . 'uploaded successfully to: ' . config('laravel-deployer.public_path') . '/public/' . basename($file));
            }


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
