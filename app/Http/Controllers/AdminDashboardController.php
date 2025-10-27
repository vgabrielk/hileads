<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\WhatsAppConnection;
use App\Models\WhatsAppGroup;
use App\Models\ExtractedContact;
use App\Models\MassSending;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        // Estatísticas gerais
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'admin_users' => User::where('is_admin', true)->count(),
            'total_plans' => Plan::count(),
            'active_plans' => Plan::where('is_active', true)->count(),
            'total_subscriptions' => Subscription::count(),
            'active_subscriptions' => Subscription::where('status', 'active')->count(),
            'total_whatsapp_connections' => WhatsAppConnection::count(),
            'active_whatsapp_connections' => WhatsAppConnection::where('status', 'connected')->count(),
            'total_groups' => WhatsAppGroup::count(),
            'total_contacts' => ExtractedContact::count(),
            'total_campaigns' => MassSending::count(),
        ];

        // Receita mensal
        $monthlyRevenue = Subscription::where('status', 'active')
            ->where('created_at', '>=', Carbon::now()->startOfMonth())
            ->with('plan')
            ->get()
            ->sum(function ($subscription) {
                return $subscription->plan->price ?? 0;
            });

        // Receita total
        $totalRevenue = Subscription::where('status', 'active')
            ->with('plan')
            ->get()
            ->sum(function ($subscription) {
                return $subscription->plan->price ?? 0;
            });

        // Usuários por plano
        $usersByPlan = Plan::withCount(['subscriptions' => function ($query) {
            $query->where('status', 'active');
        }])->get();

        // Novos usuários nos últimos 30 dias
        $newUsersLast30Days = User::where('created_at', '>=', Carbon::now()->subDays(30))->count();

        // Assinaturas por status
        $subscriptionsByStatus = Subscription::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        // Atividade recente
        $recentUsers = User::latest()->take(5)->get();
        $recentSubscriptions = Subscription::with(['user', 'plan'])
            ->latest()
            ->take(5)
            ->get();
        $recentConnections = WhatsAppConnection::with('user')
            ->latest()
            ->take(5)
            ->get();

        // Gráfico de usuários por mês (últimos 12 meses)
        $usersByMonth = User::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        // Gráfico de receita por mês (últimos 12 meses)
        $revenueByMonth = Subscription::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(plans.price) as revenue')
            )
            ->join('plans', 'subscriptions.plan_id', '=', 'plans.id')
            ->where('subscriptions.status', 'active')
            ->where('subscriptions.created_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        // Top planos mais populares
        $popularPlans = Plan::withCount(['subscriptions' => function ($query) {
            $query->where('status', 'active');
        }])
        ->orderBy('subscriptions_count', 'desc')
        ->take(5)
        ->get();

        // Usuários com mais atividade
        $mostActiveUsers = User::withCount(['whatsappConnections', 'massSendings', 'extractedContacts'])
            ->orderBy('whatsapp_connections_count', 'desc')
            ->orderBy('mass_sendings_count', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'monthlyRevenue',
            'totalRevenue',
            'usersByPlan',
            'newUsersLast30Days',
            'subscriptionsByStatus',
            'recentUsers',
            'recentSubscriptions',
            'recentConnections',
            'usersByMonth',
            'revenueByMonth',
            'popularPlans',
            'mostActiveUsers'
        ));
    }
}