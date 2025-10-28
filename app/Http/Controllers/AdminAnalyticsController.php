<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Models\WhatsAppConnection;
use App\Models\WhatsAppGroup;
use App\Models\ExtractedContact;
use App\Models\MassSending;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminAnalyticsController extends Controller
{
    /**
     * Display analytics dashboard.
     */
    public function index(Request $request)
    {
        $period = $request->get('period', '30'); // days
        $startDate = Carbon::now()->subDays($period);
        $endDate = Carbon::now();

        // Revenue Analytics
        $revenueData = $this->getRevenueAnalytics($startDate, $endDate);
        
        // User Analytics
        $userData = $this->getUserAnalytics($startDate, $endDate);
        
        // Campaign Analytics
        $campaignData = $this->getCampaignAnalytics($startDate, $endDate);
        
        // WhatsApp Analytics
        $whatsappData = $this->getWhatsAppAnalytics($startDate, $endDate);
        
        // Conversion Analytics
        $conversionData = $this->getConversionAnalytics($startDate, $endDate);

        return view('admin.analytics.index', compact(
            'revenueData',
            'userData', 
            'campaignData',
            'whatsappData',
            'conversionData',
            'period'
        ));
    }

    /**
     * Get revenue analytics data.
     */
    private function getRevenueAnalytics($startDate, $endDate)
    {
        // Total revenue
        $totalRevenue = Subscription::join('plans', 'subscriptions.plan_id', '=', 'plans.id')
            ->where('subscriptions.status', 'active')
            ->sum('plans.price');

        // Revenue in period
        $periodRevenue = Subscription::join('plans', 'subscriptions.plan_id', '=', 'plans.id')
            ->where('subscriptions.status', 'active')
            ->whereBetween('subscriptions.created_at', [$startDate, $endDate])
            ->sum('plans.price');

        // Revenue by month (last 12 months)
        $revenueByMonth = Subscription::select(
                DB::raw('YEAR(subscriptions.created_at) as year'),
                DB::raw('MONTH(subscriptions.created_at) as month'),
                DB::raw('SUM(plans.price) as revenue'),
                DB::raw('COUNT(*) as subscriptions_count')
            )
            ->join('plans', 'subscriptions.plan_id', '=', 'plans.id')
            ->where('subscriptions.status', 'active')
            ->where('subscriptions.created_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        // Revenue by plan
        $revenueByPlan = Plan::select(
                'plans.name',
                'plans.price',
                DB::raw('COUNT(subscriptions.id) as subscriptions_count'),
                DB::raw('SUM(plans.price) as total_revenue')
            )
            ->leftJoin('subscriptions', function($join) {
                $join->on('plans.id', '=', 'subscriptions.plan_id')
                     ->where('subscriptions.status', 'active');
            })
            ->groupBy('plans.id', 'plans.name', 'plans.price')
            ->orderBy('total_revenue', 'desc')
            ->get();

        // Average revenue per user
        $activeUsers = User::where('is_active', true)->count();
        $avgRevenuePerUser = $activeUsers > 0 ? $totalRevenue / $activeUsers : 0;

        return [
            'total_revenue' => $totalRevenue,
            'period_revenue' => $periodRevenue,
            'revenue_by_month' => $revenueByMonth,
            'revenue_by_plan' => $revenueByPlan,
            'avg_revenue_per_user' => $avgRevenuePerUser,
        ];
    }

    /**
     * Get user analytics data.
     */
    private function getUserAnalytics($startDate, $endDate)
    {
        // User growth
        $userGrowth = User::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // User status distribution
        $userStatus = User::select(
                'is_active',
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('is_active')
            ->get();

        // User role distribution
        $userRoles = User::select(
                'role',
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('role')
            ->get();

        // New users by month (last 12 months)
        $newUsersByMonth = User::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        // Top users by activity
        $topActiveUsers = User::withCount(['massSendings', 'whatsappConnections'])
            ->orderBy('mass_sendings_count', 'desc')
            ->take(10)
            ->get();

        return [
            'user_growth' => $userGrowth,
            'user_status' => $userStatus,
            'user_roles' => $userRoles,
            'new_users_by_month' => $newUsersByMonth,
            'top_active_users' => $topActiveUsers,
        ];
    }

    /**
     * Get campaign analytics data.
     */
    private function getCampaignAnalytics($startDate, $endDate)
    {
        // Campaign statistics
        $totalCampaigns = MassSending::count();
        $periodCampaigns = MassSending::whereBetween('created_at', [$startDate, $endDate])->count();
        
        $campaignsByStatus = MassSending::select(
                'status',
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('status')
            ->get();

        // Campaign performance - use COALESCE to handle null values
        $campaignPerformance = MassSending::select(
                DB::raw('AVG(COALESCE(total_recipients, 0)) as avg_recipients'),
                DB::raw('AVG(COALESCE(sent_count, 0)) as avg_sent'),
                DB::raw('AVG(COALESCE(failed_count, 0)) as avg_failed'),
                DB::raw('SUM(COALESCE(total_recipients, 0)) as total_recipients'),
                DB::raw('SUM(COALESCE(sent_count, 0)) as total_sent'),
                DB::raw('SUM(COALESCE(failed_count, 0)) as total_failed')
            )
            ->first();

        // Campaigns by message type
        $campaignsByType = MassSending::select(
                'message_type',
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('message_type')
            ->get();

        // Campaigns by day
        $campaignsByDay = MassSending::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Success rate
        $successRate = $totalCampaigns > 0 ? 
            (MassSending::where('status', 'completed')->count() / $totalCampaigns) * 100 : 0;

        return [
            'total_campaigns' => $totalCampaigns,
            'period_campaigns' => $periodCampaigns,
            'campaigns_by_status' => $campaignsByStatus,
            'campaign_performance' => $campaignPerformance,
            'campaigns_by_type' => $campaignsByType,
            'campaigns_by_day' => $campaignsByDay,
            'success_rate' => $successRate,
        ];
    }

    /**
     * Get WhatsApp analytics data.
     */
    private function getWhatsAppAnalytics($startDate, $endDate)
    {
        // Connection statistics
        $totalConnections = WhatsAppConnection::count();
        $activeConnections = WhatsAppConnection::where('status', 'connected')->count();
        
        $connectionsByStatus = WhatsAppConnection::select(
                'status',
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('status')
            ->get();

        // Group statistics
        $totalGroups = WhatsAppGroup::count();
        $groupsByConnection = WhatsAppGroup::select(
                'whatsapp_connection_id',
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('whatsapp_connection_id')
            ->get();

        // Contact statistics
        $totalContacts = ExtractedContact::count();
        $contactsByConnection = ExtractedContact::select(
                'whatsapp_connection_id',
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('whatsapp_connection_id')
            ->get();

        return [
            'total_connections' => $totalConnections,
            'active_connections' => $activeConnections,
            'connections_by_status' => $connectionsByStatus,
            'total_groups' => $totalGroups,
            'groups_by_connection' => $groupsByConnection,
            'total_contacts' => $totalContacts,
            'contacts_by_connection' => $contactsByConnection,
        ];
    }

    /**
     * Get conversion analytics data.
     */
    private function getConversionAnalytics($startDate, $endDate)
    {
        // User conversion funnel
        $totalUsers = User::count();
        $usersWithConnections = User::whereHas('whatsappConnections')->count();
        $usersWithSubscriptions = User::whereHas('subscriptions', function($query) {
            $query->where('status', 'active');
        })->count();
        $usersWithCampaigns = User::whereHas('massSendings')->count();

        // Conversion rates
        $connectionRate = $totalUsers > 0 ? ($usersWithConnections / $totalUsers) * 100 : 0;
        $subscriptionRate = $totalUsers > 0 ? ($usersWithSubscriptions / $totalUsers) * 100 : 0;
        $campaignRate = $totalUsers > 0 ? ($usersWithCampaigns / $totalUsers) * 100 : 0;

        // Time to first action
        $avgTimeToConnection = User::whereHas('whatsappConnections')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, (SELECT MIN(created_at) FROM whatsapp_connections WHERE user_id = users.id))) as avg_hours')
            ->value('avg_hours');

        $avgTimeToSubscription = User::whereHas('subscriptions')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, (SELECT MIN(created_at) FROM subscriptions WHERE user_id = users.id))) as avg_hours')
            ->value('avg_hours');

        // Retention rates
        $retentionData = $this->calculateRetentionRates();

        return [
            'total_users' => $totalUsers,
            'users_with_connections' => $usersWithConnections,
            'users_with_subscriptions' => $usersWithSubscriptions,
            'users_with_campaigns' => $usersWithCampaigns,
            'connection_rate' => $connectionRate,
            'subscription_rate' => $subscriptionRate,
            'campaign_rate' => $campaignRate,
            'avg_time_to_connection' => $avgTimeToConnection,
            'avg_time_to_subscription' => $avgTimeToSubscription,
            'retention_data' => $retentionData,
        ];
    }

    /**
     * Calculate user retention rates.
     */
    private function calculateRetentionRates()
    {
        $cohorts = [];
        
        // Get cohorts for last 6 months
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $startOfMonth = $month->copy()->startOfMonth();
            $endOfMonth = $month->copy()->endOfMonth();
            
            $newUsers = User::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
            
            if ($newUsers > 0) {
                $retainedUsers = User::whereBetween('created_at', [$startOfMonth, $endOfMonth])
                    ->where('last_login_at', '>=', $month->copy()->addMonth())
                    ->count();
                
                $cohorts[] = [
                    'month' => $month->format('M Y'),
                    'new_users' => $newUsers,
                    'retained_users' => $retainedUsers,
                    'retention_rate' => ($retainedUsers / $newUsers) * 100
                ];
            }
        }
        
        return $cohorts;
    }

    /**
     * Export analytics data.
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');
        $type = $request->get('type', 'revenue');
        
        // This would implement actual export functionality
        // For now, return a placeholder response
        return response()->json([
            'message' => 'Export functionality will be implemented',
            'type' => $type,
            'format' => $format
        ]);
    }
}