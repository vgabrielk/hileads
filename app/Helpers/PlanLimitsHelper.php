<?php

namespace App\Helpers;

use App\Models\MassSending;
use App\Models\User;

class PlanLimitsHelper
{
    /**
     * Verificar se o usuário pode criar uma nova campanha baseado no limite do plano
     */
    public static function canCreateCampaign(User $user): array
    {
        $activeSubscription = $user->activeSubscription;
        
        if (!$activeSubscription || !$activeSubscription->plan->max_campaigns) {
            return [
                'can_create' => true,
                'message' => null
            ];
        }

        $currentMonthCampaigns = MassSending::where('user_id', $user->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        if ($currentMonthCampaigns >= $activeSubscription->plan->max_campaigns) {
            return [
                'can_create' => false,
                'message' => "Você atingiu o limite de {$activeSubscription->plan->max_campaigns} campanhas por mês do seu plano {$activeSubscription->plan->name}. Upgrade seu plano para criar mais campanhas.",
                'current_count' => $currentMonthCampaigns,
                'max_allowed' => $activeSubscription->plan->max_campaigns,
                'plan_name' => $activeSubscription->plan->name
            ];
        }

        return [
            'can_create' => true,
            'message' => null,
            'current_count' => $currentMonthCampaigns,
            'max_allowed' => $activeSubscription->plan->max_campaigns,
            'remaining' => $activeSubscription->plan->max_campaigns - $currentMonthCampaigns
        ];
    }

    /**
     * Verificar se o usuário pode criar um novo envio em massa baseado no limite do plano
     */
    public static function canCreateMassSending(User $user): array
    {
        $activeSubscription = $user->activeSubscription;
        
        if (!$activeSubscription || !$activeSubscription->plan->max_mass_sendings) {
            return [
                'can_create' => true,
                'message' => null
            ];
        }

        $currentMonthMassSendings = MassSending::where('user_id', $user->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        if ($currentMonthMassSendings >= $activeSubscription->plan->max_mass_sendings) {
            return [
                'can_create' => false,
                'message' => "Você atingiu o limite de {$activeSubscription->plan->max_mass_sendings} envios em massa por mês do seu plano {$activeSubscription->plan->name}. Upgrade seu plano para criar mais envios.",
                'current_count' => $currentMonthMassSendings,
                'max_allowed' => $activeSubscription->plan->max_mass_sendings,
                'plan_name' => $activeSubscription->plan->name
            ];
        }

        return [
            'can_create' => true,
            'message' => null,
            'current_count' => $currentMonthMassSendings,
            'max_allowed' => $activeSubscription->plan->max_mass_sendings,
            'remaining' => $activeSubscription->plan->max_mass_sendings - $currentMonthMassSendings
        ];
    }

    /**
     * Obter estatísticas de uso do plano do usuário
     */
    public static function getPlanUsageStats(User $user): array
    {
        $activeSubscription = $user->activeSubscription;
        
        if (!$activeSubscription) {
            return [
                'has_subscription' => false,
                'plan_name' => null,
                'campaigns' => null,
                'mass_sendings' => null,
                'contacts' => null
            ];
        }

        $currentMonth = now()->month;
        $currentYear = now()->year;

        $campaignsCount = MassSending::where('user_id', $user->id)
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->count();

        return [
            'has_subscription' => true,
            'plan_name' => $activeSubscription->plan->name,
            'campaigns' => [
                'current' => $campaignsCount,
                'max' => $activeSubscription->plan->max_campaigns,
                'remaining' => $activeSubscription->plan->max_campaigns ? 
                    max(0, $activeSubscription->plan->max_campaigns - $campaignsCount) : null,
                'unlimited' => $activeSubscription->plan->max_campaigns === null
            ],
            'mass_sendings' => [
                'current' => $campaignsCount, // Mass sendings são as campanhas
                'max' => $activeSubscription->plan->max_mass_sendings,
                'remaining' => $activeSubscription->plan->max_mass_sendings ? 
                    max(0, $activeSubscription->plan->max_mass_sendings - $campaignsCount) : null,
                'unlimited' => $activeSubscription->plan->max_mass_sendings === null
            ],
            'contacts' => [
                'max' => $activeSubscription->plan->max_contacts,
                'unlimited' => $activeSubscription->plan->max_contacts === null
            ]
        ];
    }
}




