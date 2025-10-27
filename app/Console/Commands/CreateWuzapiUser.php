<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class CreateWuzapiUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wuzapi:create-user {name} {token}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new user in Wuzapi';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $token = $this->argument('token');
        
        $baseUrl = config('services.wuzapi.base_url', 'https://api.wuzapi.com');
        $adminToken = config('services.wuzapi.admin_token', 'your_admin_token_here');
        
        try {
            $response = Http::withHeaders([
                'Authorization' => $adminToken,
                'Content-Type' => 'application/json',
            ])->post($baseUrl . '/admin/users', [
                'name' => $name,
                'token' => $token,
            ]);

            if ($response->successful()) {
                $this->info("Usuário '{$name}' criado com sucesso!");
                $this->info("Token: {$token}");
                $this->info("Configure este token no arquivo .env como WUZAPI_TOKEN={$token}");
            } else {
                $this->error('Erro ao criar usuário: ' . $response->body());
            }
        } catch (\Exception $e) {
            $this->error('Erro: ' . $e->getMessage());
        }
    }
}