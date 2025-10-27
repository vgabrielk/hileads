<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class GenerateUserTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:generate-tokens';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gera tokens de API para usuários que não possuem';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Verificando usuários sem token...');

        $usersWithoutToken = User::whereNull('api_token')->get();

        if ($usersWithoutToken->isEmpty()) {
            $this->info('Todos os usuários já possuem tokens.');
            return 0;
        }

        $this->info('Encontrados ' . $usersWithoutToken->count() . ' usuários sem token.');

        foreach ($usersWithoutToken as $user) {
            $token = $user->generateApiToken();
            $this->info("Token gerado para {$user->name} ({$user->email}): {$token}");
        }

        $this->info('Todos os tokens foram gerados com sucesso!');

        return 0;
    }
}
