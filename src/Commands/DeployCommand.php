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
        $this->info('🚀 Starting deployment process...');

        try {
            // Connect remote server
            $this->info('📡 Connecting to remote server...');
            $server = $this->connectToServer();
            $this->info('✅ Connected successfully');

            // Get excluded directories
            $defaultExcludes = ['vendor', 'node_modules', 'storage'];
            $excludedDirectories = $this->askWithCompletion(
                '📂 Enter directories to exclude (comma-separated)',
                $defaultExcludes
            );
            $excludedDirectories = array_unique(array_merge(
                array_map('trim', explode(',', $excludedDirectories)),
                $defaultExcludes
            ));

            $this->info('🔒 Excluded directories: ' . implode(', ', $excludedDirectories));
            $this->newLine();

            // Upload directories
            $directories = glob(base_path() . '/*', GLOB_ONLYDIR);
            $dirCount = count(array_filter($directories, fn($dir) => !in_array(basename($dir), $excludedDirectories)));
            
            $dirBar = $this->output->createProgressBar($dirCount);
            $dirBar->setFormat('📤 Uploading directories: [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s%');
            
            $this->newLine();
            $dirBar->start();

            foreach ($directories as $directory) {
                if (in_array(basename($directory), $excludedDirectories)) {
                    continue;
                }

                $server->uploadDirectory(
                    $directory, 
                    config('laravel-deployer.src_path') . '/' . basename($directory),
                    function($filename) {
                        $this->line("   ↪ Uploading: " . basename($filename), null, OutputInterface::VERBOSITY_VERBOSE);
                    }
                );
                
                $dirBar->advance();
            }
            
            $dirBar->finish();
            $this->newLine(2);

            // Upload root files
            $defaultExcludedFiles = ['.env', '.env.example'];
            $excludedFiles = $this->askWithCompletion(
                '📄 Enter files to exclude (comma-separated)',
                $defaultExcludedFiles
            );
            $excludedFiles = array_unique(array_merge(
                array_map('trim', explode(',', $excludedFiles)),
                $defaultExcludedFiles
            ));

            $this->info('🔒 Excluded files: ' . implode(', ', $excludedFiles));
            $this->newLine();

            $this->info('📄 Uploading root files...');
            $rootFiles = array_filter(glob(base_path() . '/*'), 'is_file');
            $fileBar = $this->output->createProgressBar(count($rootFiles));
            $fileBar->setFormat(' [%bar%] %current%/%max% files');
            
            foreach ($rootFiles as $file) {
                if (!in_array(basename($file), $excludedFiles)) {
                    $server->upload($file, config('laravel-deployer.src_path') . '/' . basename($file));
                    $fileBar->advance();
                }
            }
            
            $fileBar->finish();
            $this->newLine(2);

            // Upload public files
            $this->info('🌐 Uploading public assets...');
            $publicFiles = array_filter(glob(public_path() . '/*'), function($file) {
                return basename($file) !== 'index.php';
            });
            
            foreach ($publicFiles as $file) {
                $targetPath = config('laravel-deployer.public_path') . '/public/' . basename($file);
                $server->upload($file, $targetPath);
                $this->line("   ✓ " . basename($file));
            }

            $this->newLine();
            $this->info('🔄 Running post-deployment tasks...');
            $this->runPostDeploymentCommands();

            $server->disconnect();
            $this->newLine();
            $this->info('✨ Deployment completed successfully!');

            // Provider user ssh connection details including credentials so that they can run additional commands
            $this->newLine();
            $this->info('🔑 SSH Connection Details');
            $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
            $this->line('   Host     : ' . config('laravel-deployer.ftp.host'));
            $this->line('   Username : ' . config('laravel-deployer.ftp.username'));
            $this->line('   Command  : ssh ' . config('laravel-deployer.ftp.username') . '@' . config('laravel-deployer.ftp.host'));
            $this->line('   Password : ' . config('laravel-deployer.ftp.password'));
            $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
            $this->comment('   Tip: Use this connection to run additional commands if needed.');

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
