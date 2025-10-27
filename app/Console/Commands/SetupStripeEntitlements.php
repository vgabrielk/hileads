<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Plan;
use App\Services\StripeEntitlementsService;

class SetupStripeEntitlements extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stripe:setup-entitlements {--plan=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup Stripe entitlements and features for plans';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Setting up Stripe Entitlements...');
        
        $entitlementsService = app(StripeEntitlementsService::class);
        
        $planId = $this->option('plan');
        
        if ($planId) {
            $plan = Plan::find($planId);
            if (!$plan) {
                $this->error("Plan with ID {$planId} not found");
                return 1;
            }
            $plans = collect([$plan]);
        } else {
            $plans = Plan::all();
        }
        
        $this->info("Processing {$plans->count()} plans...");
        
        foreach ($plans as $plan) {
            $this->info("Setting up features for plan: {$plan->name} (R$ {$plan->price})");
            
            try {
                $features = $entitlementsService->setupPlanFeatures($plan);
                
                $this->info("✅ Created " . count($features) . " features for plan {$plan->name}");
                
                foreach ($features as $feature) {
                    $this->line("  • {$feature->name} ({$feature->lookup_key})");
                }
                
            } catch (\Exception $e) {
                $this->error("❌ Failed to setup features for plan {$plan->name}: " . $e->getMessage());
            }
        }
        
        $this->info('');
        $this->info('🎉 Stripe Entitlements setup completed!');
        $this->info('');
        $this->info('Next steps:');
        $this->info('1. Test customer entitlements with: php artisan stripe:test-entitlements');
        $this->info('2. Configure webhooks for entitlements.active_entitlement_summary.updated');
        $this->info('3. Update your application to check entitlements before granting access');
        
        return 0;
    }
}
