<?php

namespace Codehubcare\LaravelDeployer\Commands;

use Illuminate\Console\Command;

class SshDetails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravel-deployer:ssh-details';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display SSH connection details';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->newLine();
        $this->info('SSH Connection Details');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->line('   Host     : ' . config('laravel-deployer.ftp.host'));
        $this->line('   Username : ' . config('laravel-deployer.ftp.username'));
        $this->line('   Command  : ssh ' . config('laravel-deployer.ftp.username') . '@' . config('laravel-deployer.ftp.host'));
        $this->line('   Password : ' . config('laravel-deployer.ftp.password'));
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->comment('   Tip: Use this connection to run additional commands if needed.');

        return 0;
    }
}
