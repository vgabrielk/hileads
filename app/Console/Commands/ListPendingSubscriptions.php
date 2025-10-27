<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription;

class ListPendingSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:list-pending {--limit=10}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List pending subscriptions that need to be activated';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = $this->option('limit');
        
        $this->info('Pending Subscriptions:');
        $this->info('====================');
        
        $pendingSubscriptions = Subscription::where('status', 'pending')
            ->whereNotNull('stripe_session_id')
            ->with(['user', 'plan'])
            ->latest()
            ->limit($limit)
            ->get();
            
        if ($pendingSubscriptions->isEmpty()) {
            $this->info('No pending subscriptions found');
            return 0;
        }
        
        $this->info("Found {$pendingSubscriptions->count()} pending subscriptions:");
        $this->info('');
        
        foreach ($pendingSubscriptions as $subscription) {
            $this->line("ID: {$subscription->id}");
            $this->line("User: {$subscription->user->name} ({$subscription->user->email})");
            $this->line("Plan: {$subscription->plan->name} (R$ {$subscription->plan->price})");
            $this->line("Session: {$subscription->stripe_session_id}");
            $this->line("Created: {$subscription->created_at}");
            $this->line("---");
        }
        
        $this->info('');
        $this->info('To activate a subscription, run:');
        $this->info('php artisan subscription:activate {ID}');
        $this->info('');
        $this->info('To process all pending subscriptions, run:');
        $this->info('php artisan subscriptions:process-pending');
        
        return 0;
    }
}
