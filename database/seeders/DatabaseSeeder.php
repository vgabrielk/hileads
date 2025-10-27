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
        // Chamar outros seeders primeiro
        $this->call([
            PlanSeeder::class,
            AdminUserSeeder::class,
        ]);

        // Criar um usuário normal para teste (se não existir)
        $user = User::firstOrCreate(
            ['email' => 'user@hileads.com'],
            [
                'name' => 'Usuário Teste',
                'password' => Hash::make('user123'),
                'role' => 'user',
            ]
        );

        if ($user->wasRecentlyCreated) {
            // Gerar token para o usuário apenas se foi criado agora
            $userToken = $user->generateApiToken();
            if ($this->command) {
                $this->command->info('✓ Usuário de Teste criado com sucesso!');
                $this->command->info('  Email: user@hileads.com');
                $this->command->info('  Senha: user123');
                $this->command->info('  Token: ' . $userToken);
            }
        } else {
            if ($this->command) {
                $this->command->info('✓ Usuário de Teste já existe (pulando criação)');
            }
        }
    }
}