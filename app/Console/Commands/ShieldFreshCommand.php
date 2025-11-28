<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

class ShieldFreshCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shield:fresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fresh Shield Generate';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->call('shield:generate', [
            '--all' => true,
        ]);

        if (app()->isLocal()) {
            $this->pint();
        } else {
            $this->gitCheckout();
        }

        $this->superAdmin();
    }

    public function pint()
    {
        $pint = Process::run([
            './vendor/bin/pint',
            './app/Policies',
            '--parallel',
        ]);

        $this->line(PHP_EOL);
        $this->line('Pinting Policies...');

        if ($output = $pint->output()) {
            $this->line($output);
        }

        if ($error = $pint->errorOutput()) {
            $this->error($error);
        }
    }

    public function gitCheckout()
    {
        if (file_exists('/usr/bin/git')) {
            $this->info('Checking out...');
            Process::run(['/usr/bin/git', 'checkout', '.']);
        } else {
            $this->error('Git not found');
        }
    }

    public function superAdmin()
    {
        $superAdminExists = User::role('super_admin')->exists();

        if ($superAdminExists) {
            $this->info('Super Admin already exists');

            return;
        }

        $this->info('Assigning Super Admin...');
        $this->call('shield:super-admin');
    }
}
