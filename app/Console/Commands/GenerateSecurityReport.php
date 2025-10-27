<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GenerateSecurityReport extends Command
{
    protected $signature = 'subscription:security-report {--output=console}';
    protected $description = 'Gera relatório de segurança do sistema de assinaturas';

    public function handle()
    {
        $this->info('🔒 Gerando relatório de segurança do sistema de assinaturas...');
        
        $output = $this->option('output');
        $report = $this->generateSecurityReport();
        
        if ($output === 'console') {
            $this->displayReport($report);
        } else {
            $this->saveReportToFile($report, $output);
        }
        
        $this->info('✅ Relatório de segurança gerado com sucesso!');
        return 0;
    }
    
    private function generateSecurityReport(): array
    {
        $report = [
            'timestamp' => now()->toISOString(),
            'summary' => $this->generateSummary(),
            'user_analysis' => $this->analyzeUsers(),
            'subscription_analysis' => $this->analyzeSubscriptions(),
            'security_issues' => $this->identifySecurityIssues(),
            'recommendations' => $this->generateRecommendations(),
        ];
        
        return $report;
    }
    
    private function generateSummary(): array
    {
        $totalUsers = User::count();
        $activeUsers = User::where('is_active', true)->count();
        $adminUsers = User::where('role', 'admin')->count();
        $usersWithSubscriptions = User::whereHas('subscriptions')->count();
        $usersWithActiveSubscriptions = User::whereHas('subscriptions', function($query) {
            $query->where('status', 'active')->where('expires_at', '>', now());
        })->count();
        
        return [
            'total_users' => $totalUsers,
            'active_users' => $activeUsers,
            'admin_users' => $adminUsers,
            'users_with_subscriptions' => $usersWithSubscriptions,
            'users_with_active_subscriptions' => $usersWithActiveSubscriptions,
            'subscription_rate' => $totalUsers > 0 ? round(($usersWithActiveSubscriptions / $totalUsers) * 100, 2) : 0,
        ];
    }
    
    private function analyzeUsers(): array
    {
        $inactiveUsers = User::where('is_active', false)->count();
        $usersWithoutSubscriptions = User::whereDoesntHave('subscriptions')->where('role', '!=', 'admin')->count();
        $usersWithExpiredSubscriptions = User::whereHas('subscriptions', function($query) {
            $query->where('status', 'active')->where('expires_at', '<', now());
        })->count();
        
        return [
            'inactive_users' => $inactiveUsers,
            'users_without_subscriptions' => $usersWithoutSubscriptions,
            'users_with_expired_subscriptions' => $usersWithExpiredSubscriptions,
            'potential_security_risks' => $inactiveUsers + $usersWithExpiredSubscriptions,
        ];
    }
    
    private function analyzeSubscriptions(): array
    {
        $totalSubscriptions = Subscription::count();
        $activeSubscriptions = Subscription::where('status', 'active')->where('expires_at', '>', now())->count();
        $expiredSubscriptions = Subscription::where('status', 'active')->where('expires_at', '<', now())->count();
        $cancelledSubscriptions = Subscription::where('status', 'cancelled')->count();
        $pendingSubscriptions = Subscription::where('status', 'pending')->count();
        
        $expiringSoon = Subscription::where('status', 'active')
            ->where('expires_at', '>', now())
            ->where('expires_at', '<', now()->addDays(7))
            ->count();
        
        return [
            'total_subscriptions' => $totalSubscriptions,
            'active_subscriptions' => $activeSubscriptions,
            'expired_subscriptions' => $expiredSubscriptions,
            'cancelled_subscriptions' => $cancelledSubscriptions,
            'pending_subscriptions' => $pendingSubscriptions,
            'expiring_soon' => $expiringSoon,
            'health_score' => $totalSubscriptions > 0 ? round(($activeSubscriptions / $totalSubscriptions) * 100, 2) : 0,
        ];
    }
    
    private function identifySecurityIssues(): array
    {
        $issues = [];
        
        // Verificar usuários com múltiplas assinaturas ativas
        $usersWithMultipleActiveSubscriptions = DB::table('subscriptions')
            ->select('user_id', DB::raw('COUNT(*) as count'))
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->groupBy('user_id')
            ->having('count', '>', 1)
            ->get();
        
        if ($usersWithMultipleActiveSubscriptions->count() > 0) {
            $issues[] = [
                'type' => 'multiple_active_subscriptions',
                'severity' => 'high',
                'description' => 'Usuários com múltiplas assinaturas ativas',
                'count' => $usersWithMultipleActiveSubscriptions->count(),
                'users' => $usersWithMultipleActiveSubscriptions->pluck('user_id')->toArray(),
            ];
        }
        
        // Verificar assinaturas expiradas que ainda estão marcadas como ativas
        $expiredButActive = Subscription::where('status', 'active')
            ->where('expires_at', '<', now())
            ->count();
        
        if ($expiredButActive > 0) {
            $issues[] = [
                'type' => 'expired_but_active',
                'severity' => 'medium',
                'description' => 'Assinaturas expiradas ainda marcadas como ativas',
                'count' => $expiredButActive,
            ];
        }
        
        // Verificar usuários inativos com assinaturas ativas
        $inactiveUsersWithActiveSubscriptions = User::where('is_active', false)
            ->whereHas('subscriptions', function($query) {
                $query->where('status', 'active')->where('expires_at', '>', now());
            })
            ->count();
        
        if ($inactiveUsersWithActiveSubscriptions > 0) {
            $issues[] = [
                'type' => 'inactive_users_with_subscriptions',
                'severity' => 'medium',
                'description' => 'Usuários inativos com assinaturas ativas',
                'count' => $inactiveUsersWithActiveSubscriptions,
            ];
        }
        
        return $issues;
    }
    
    private function generateRecommendations(): array
    {
        $recommendations = [];
        
        $summary = $this->generateSummary();
        $subscriptionAnalysis = $this->analyzeSubscriptions();
        
        if ($summary['subscription_rate'] < 50) {
            $recommendations[] = [
                'priority' => 'high',
                'category' => 'business',
                'title' => 'Baixa taxa de conversão de assinaturas',
                'description' => 'Apenas ' . $summary['subscription_rate'] . '% dos usuários têm assinaturas ativas. Considere melhorar a estratégia de conversão.',
            ];
        }
        
        if ($subscriptionAnalysis['expiring_soon'] > 0) {
            $recommendations[] = [
                'priority' => 'medium',
                'category' => 'retention',
                'title' => 'Assinaturas expirando em breve',
                'description' => $subscriptionAnalysis['expiring_soon'] . ' assinaturas expiram nos próximos 7 dias. Implemente notificações de renovação.',
            ];
        }
        
        if ($subscriptionAnalysis['expired_subscriptions'] > 0) {
            $recommendations[] = [
                'priority' => 'high',
                'category' => 'security',
                'title' => 'Assinaturas expiradas não atualizadas',
                'description' => $subscriptionAnalysis['expired_subscriptions'] . ' assinaturas expiradas ainda estão marcadas como ativas. Execute limpeza automática.',
            ];
        }
        
        $recommendations[] = [
            'priority' => 'medium',
            'category' => 'monitoring',
            'title' => 'Implementar monitoramento contínuo',
            'description' => 'Configure alertas automáticos para detectar anomalias no sistema de assinaturas.',
        ];
        
        return $recommendations;
    }
    
    private function displayReport(array $report): void
    {
        $this->info('📊 RELATÓRIO DE SEGURANÇA DO SISTEMA DE ASSINATURAS');
        $this->info('=' . str_repeat('=', 60));
        
        $this->info("\n📈 RESUMO GERAL:");
        $summary = $report['summary'];
        $this->info("• Total de usuários: {$summary['total_users']}");
        $this->info("• Usuários ativos: {$summary['active_users']}");
        $this->info("• Administradores: {$summary['admin_users']}");
        $this->info("• Usuários com assinaturas ativas: {$summary['users_with_active_subscriptions']}");
        $this->info("• Taxa de assinatura: {$summary['subscription_rate']}%");
        
        $this->info("\n👥 ANÁLISE DE USUÁRIOS:");
        $userAnalysis = $report['user_analysis'];
        $this->info("• Usuários inativos: {$userAnalysis['inactive_users']}");
        $this->info("• Usuários sem assinaturas: {$userAnalysis['users_without_subscriptions']}");
        $this->info("• Usuários com assinaturas expiradas: {$userAnalysis['users_with_expired_subscriptions']}");
        $this->info("• Riscos de segurança identificados: {$userAnalysis['potential_security_risks']}");
        
        $this->info("\n📦 ANÁLISE DE ASSINATURAS:");
        $subscriptionAnalysis = $report['subscription_analysis'];
        $this->info("• Total de assinaturas: {$subscriptionAnalysis['total_subscriptions']}");
        $this->info("• Assinaturas ativas: {$subscriptionAnalysis['active_subscriptions']}");
        $this->info("• Assinaturas expiradas: {$subscriptionAnalysis['expired_subscriptions']}");
        $this->info("• Assinaturas canceladas: {$subscriptionAnalysis['cancelled_subscriptions']}");
        $this->info("• Assinaturas pendentes: {$subscriptionAnalysis['pending_subscriptions']}");
        $this->info("• Expirando em 7 dias: {$subscriptionAnalysis['expiring_soon']}");
        $this->info("• Score de saúde: {$subscriptionAnalysis['health_score']}%");
        
        if (!empty($report['security_issues'])) {
            $this->info("\n⚠️ PROBLEMAS DE SEGURANÇA IDENTIFICADOS:");
            foreach ($report['security_issues'] as $issue) {
                $severity = strtoupper($issue['severity']);
                $this->warn("• [{$severity}] {$issue['description']} ({$issue['count']} ocorrências)");
            }
        } else {
            $this->info("\n✅ Nenhum problema de segurança identificado!");
        }
        
        if (!empty($report['recommendations'])) {
            $this->info("\n💡 RECOMENDAÇÕES:");
            foreach ($report['recommendations'] as $recommendation) {
                $priority = strtoupper($recommendation['priority']);
                $this->info("• [{$priority}] {$recommendation['title']}");
                $this->info("  {$recommendation['description']}");
            }
        }
        
        $this->info("\n🕒 Relatório gerado em: {$report['timestamp']}");
    }
    
    private function saveReportToFile(array $report, string $filename): void
    {
        $json = json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        file_put_contents($filename, $json);
        $this->info("📄 Relatório salvo em: {$filename}");
    }
}
