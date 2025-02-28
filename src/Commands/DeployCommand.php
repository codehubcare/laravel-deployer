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
            $publicItems = glob(public_path() . '/*');
            
            foreach ($publicItems as $item) {

                // Skip index.php file
                if (basename($item) === 'index.php') {
                    continue;
                }
                
                $targetPath = config('laravel-deployer.public_path') . '/public/' . basename($item);
                $targetPath2 = config('laravel-deployer.public_path') . '/' . basename($item);
                
                if (is_dir($item)) {
                    $server->uploadDirectory($item, $targetPath, function($filename) {
                        $this->line("   ✓ public/" . basename($item) . '/' . basename($filename));
                    });

                    // upload to public_html folder
                    $server->uploadDirectory($item, $targetPath2, function($filename) {
                        $this->line("   ✓ public/" . basename($item) . '/' . basename($filename));
                    });
                } else {
                    $server->upload($item, $targetPath);

                    // upload to public_html folder
                    $server->upload($item, $targetPath2);

                    $this->line("   ✓ public/" . basename($item));
                }
            }

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
