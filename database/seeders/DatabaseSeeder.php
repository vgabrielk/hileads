<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Criar usuário administrador
        $admin = User::create([
            'name' => 'Administrador',
            'email' => 'admin@hileads.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        // Gerar token para o admin
        $adminToken = $admin->generateApiToken();

        $this->command->info('✓ Usuário Admin criado com sucesso!');
        $this->command->info('  Email: admin@hileads.com');
        $this->command->info('  Senha: admin123');
        $this->command->info('  Token: ' . $adminToken);
        $this->command->newLine();

        // Criar um usuário normal para teste
        $user = User::create([
            'name' => 'Usuário Teste',
            'email' => 'user@hileads.com',
            'password' => Hash::make('user123'),
            'role' => 'user',
        ]);

        // Gerar token para o usuário
        $userToken = $user->generateApiToken();

        $this->command->info('✓ Usuário de Teste criado com sucesso!');
        $this->command->info('  Email: user@hileads.com');
        $this->command->info('  Senha: user123');
        $this->command->info('  Token: ' . $userToken);
    }
}
