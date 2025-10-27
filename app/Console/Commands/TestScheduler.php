<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestScheduler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scheduler:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test if scheduler is working';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Scheduler Test');
        $this->info('================');
        
        $this->info('✅ Scheduler is working!');
        $this->info('Time: ' . now());
        
        // Log the test
        \Log::info('Scheduler test executed at ' . now());
        
        return 0;
    }
}
