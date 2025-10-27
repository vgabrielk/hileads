<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if admin already exists
        $admin = User::where('email', 'admin@hileads.com')->first();

        if (!$admin) {
            $admin = User::create([
                'name' => 'Administrador',
                'email' => 'admin@hileads.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]);

            // Generate API token
            $admin->generateApiToken();

            $this->command->info('Admin user created successfully!');
            $this->command->info('Email: admin@hileads.com');
            $this->command->info('Password: admin123');
            $this->command->info('API Token: ' . $admin->api_token);
        } else {
            $this->command->info('Admin user already exists.');
        }
    }
}
