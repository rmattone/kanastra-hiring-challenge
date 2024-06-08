<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class CheckAndRunQueue extends Command
{
    protected $signature = 'queue:check-and-run';

    protected $description = 'Check if there are any jobs in the queue and run them';

    public function handle()
    {
        $queueSize = \Illuminate\Support\Facades\Queue::size();
        
        if ($queueSize > 0) {
            $this->info("There are $queueSize jobs in the queue. Running them now...");
            
            Artisan::call('queue:work', ['--once' => true]);
        } else {
            $this->info("No jobs in the queue at the moment.");
        }
    }
}
