<?php

namespace Codehubcare\LaravelDeployer\Commands;

use Codehubcare\LaravelDeployer\Services\Ssh;
use Illuminate\Console\Command;
use Exception;

class RunCommand extends Command
{

    protected $signature = 'laravel-deployer:run';

    protected $description = 'Deploy the application';

    public function handle()
    {
        try {
            $this->info('🚀 Starting deployment process...');

            // list changed files
            $changedFiles = [];
            // Fetch latest changes from remote
            exec('git fetch origin main');
            // Get the merge-base (common ancestor) between current branch and origin/main
            $mergeBase = trim(exec('git merge-base HEAD origin/main'));
            // Get changed files using diff between merge-base and HEAD
            exec("git diff --name-only {$mergeBase} HEAD | sort | uniq", $changedFiles);

            if (empty($changedFiles)) {
                $this->info('📝 No changes detected to deploy.');
                return 0;
            }

            // Your current branch
            $this->info('📂 Current branch: ' . exec('git branch --show-current'));

            // Remote Branch
            $this->info('📂 Remote branch: ' . exec('git rev-parse --abbrev-ref origin/main'));
            $this->newLine();

            $changedFiles = collect($changedFiles)->map(function ($file) {
                return trim($file);
            });

            $this->info('📋 Changed files:');
            $changedFiles->each(function ($file) {
                $this->info('  ├─ ' . $file);
            });
            $this->newLine();

            if (!$this->confirm('Do you wish to continue with the deployment?', true)) {
                $this->info('💡 Deployment cancelled by user.');
                return 0;
            }

            // Upload changed files to server
            $this->info('📡 Connecting to remote server...');
            $server = $this->connectToServer();
            $this->info('✅ Connected successfully');
            $this->newLine();

            $this->info('📤 Uploading changed files to server...');
            $changedFiles->each(function ($file) use ($server) {
                $server->upload($file, config('laravel-deployer.src_path') . '/' . $file);
                $this->info('  ✓ ' . $file);
            });

            $this->newLine();
            $this->info('🔄 Running post-deployment tasks...');
            $this->runPostDeploymentCommands();

            $server->disconnect();
            $this->newLine();
            $this->info('✨ Deployment completed successfully!');
        } catch (Exception $ex) {
            $this->error('❌ Deployment failed: ' . $ex->getMessage());
            return 1;
        }

        return 0;
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
