<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Plan;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Plano Básico',
                'description' => 'Ideal para começar com o sistema',
                'price' => 29.90,
                'price_cents' => 2990,
                'interval' => 'monthly',
                'interval_count' => 1,
                'features' => [
                    'Até 1.000 contatos',
                    'Até 5 campanhas por mês',
                    'Até 10 envios em massa por mês',
                    'Suporte por email',
                    'Relatórios básicos'
                ],
                'max_contacts' => 1000,
                'max_campaigns' => 5,
                'max_mass_sendings' => 10,
                'is_active' => true,
                'is_popular' => false,
                'sort_order' => 1,
            ],
            [
                'name' => 'Plano Profissional',
                'description' => 'Para empresas em crescimento',
                'price' => 79.90,
                'price_cents' => 7990,
                'interval' => 'monthly',
                'interval_count' => 1,
                'features' => [
                    'Até 10.000 contatos',
                    'Campanhas ilimitadas',
                    'Até 100 envios em massa por mês',
                    'Suporte prioritário',
                    'Relatórios avançados',
                    'Automações básicas',
                    'Integração com CRM'
                ],
                'max_contacts' => 10000,
                'max_campaigns' => null,
                'max_mass_sendings' => 100,
                'is_active' => true,
                'is_popular' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Plano Empresarial',
                'description' => 'Para grandes empresas',
                'price' => 199.90,
                'price_cents' => 19990,
                'interval' => 'monthly',
                'interval_count' => 1,
                'features' => [
                    'Contatos ilimitados',
                    'Campanhas ilimitadas',
                    'Envios em massa ilimitados',
                    'Suporte 24/7',
                    'Relatórios personalizados',
                    'Automações avançadas',
                    'Integração com múltiplos CRMs',
                    'API personalizada',
                    'Gerente de conta dedicado'
                ],
                'max_contacts' => null,
                'max_campaigns' => null,
                'max_mass_sendings' => null,
                'is_active' => true,
                'is_popular' => false,
                'sort_order' => 3,
            ],
            [
                'name' => 'Plano Anual Básico',
                'description' => 'Plano básico com desconto anual',
                'price' => 299.90,
                'price_cents' => 29990,
                'interval' => 'yearly',
                'interval_count' => 1,
                'features' => [
                    'Até 1.000 contatos',
                    'Até 5 campanhas por mês',
                    'Até 10 envios em massa por mês',
                    'Suporte por email',
                    'Relatórios básicos',
                    '2 meses grátis'
                ],
                'max_contacts' => 1000,
                'max_campaigns' => 5,
                'max_mass_sendings' => 10,
                'is_active' => true,
                'is_popular' => false,
                'sort_order' => 4,
            ],
            [
                'name' => 'Plano Anual Profissional',
                'description' => 'Plano profissional com desconto anual',
                'price' => 799.90,
                'price_cents' => 79990,
                'interval' => 'yearly',
                'interval_count' => 1,
                'features' => [
                    'Até 10.000 contatos',
                    'Campanhas ilimitadas',
                    'Até 100 envios em massa por mês',
                    'Suporte prioritário',
                    'Relatórios avançados',
                    'Automações básicas',
                    'Integração com CRM',
                    '2 meses grátis'
                ],
                'max_contacts' => 10000,
                'max_campaigns' => null,
                'max_mass_sendings' => 100,
                'is_active' => true,
                'is_popular' => false,
                'sort_order' => 5,
            ]
        ];

        foreach ($plans as $planData) {
            Plan::create($planData);
        }
    }
}
