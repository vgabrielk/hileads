<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\PlanSeeder;
use Database\Seeders\AdminUserSeeder;

class SeedDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:seed-all {--force : Force the operation to run in production}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run all database seeders in the correct order';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force') && $this->laravel->environment('production')) {
            if (!$this->confirm('Are you sure you want to run seeders in production?')) {
                $this->info('Seeding cancelled.');
                return 1;
            }
        }

        $this->info('🌱 Starting database seeding...');
        $this->newLine();

        try {
            // Run seeders in order
            $this->info('📋 Seeding plans...');
            $this->call(PlanSeeder::class);
            $this->info('✅ Plans seeded successfully!');
            $this->newLine();

            $this->info('👤 Seeding admin users...');
            $this->call(AdminUserSeeder::class);
            $this->info('✅ Admin users seeded successfully!');
            $this->newLine();

            $this->info('🎯 Seeding main database...');
            $this->call(DatabaseSeeder::class);
            $this->info('✅ Main database seeded successfully!');
            $this->newLine();

            $this->info('🎉 All seeders completed successfully!');
            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Seeding failed: ' . $e->getMessage());
            return 1;
        }
    }
}