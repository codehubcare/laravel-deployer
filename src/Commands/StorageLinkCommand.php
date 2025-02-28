<?php
namespace Codehubcare\LaravelDeployer\Commands;

use Illuminate\Console\Command;

class StorageLinkCommand extends Command
{
    protected $signature = 'laravel-deployer:storage-link';
    protected $description = 'Create the symbolic links configured for the application storage';

    public function handle()
    {
        $this->line("\n<fg=blue>💫 Storage Link Creation</>");

        $this->output->write('🔄 Connecting to server...');
        try {
            $ssh = $this->connectToServer();
            $this->output->writeln(' <fg=green>✓</>' . "\n");
        } catch (\Exception $e) {
            $this->output->writeln(' <fg=red>✗</>');
            $this->error('Connection failed: ' . $e->getMessage());
            return 1;
        }

        $this->output->write('🔗 Creating symbolic link...');
        
        $target = config('laravel-deployer.src_path') . '/app/public';
        $link = config('laravel-deployer.public_path') . '/storage';

        try {
            $result = $ssh->execute("ln -s $target $link");
            
            if ($result['success']) {
                $this->output->writeln(' <fg=green>✓</>');
                $this->line("\n<fg=green>✨ Successfully created storage link:</>");
                $this->line("   $link → $target\n");
            } else {
                $this->output->writeln(' <fg=red>✗</>');
                $this->error('Failed to create symbolic link:');
                $this->line('   ' . $result['error']);
            }
        } catch (\Exception $e) {
            $this->output->writeln(' <fg=red>✗</>');
            $this->error($e->getMessage());
            return 1;
        } finally {
            $ssh->disconnect();
        }

        return 0;
    }

    private function connectToServer()
    {
        $ssh = new Ssh(
            config('laravel-deployer.ftp.host'),
            config('laravel-deployer.ftp.username'),
            config('laravel-deployer.ftp.password')
        );
        $ssh->connect();
        return $ssh;
    }
}