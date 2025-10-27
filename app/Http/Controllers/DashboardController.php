<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WhatsAppConnection;
use App\Models\WhatsAppGroup;
use App\Models\ExtractedContact;
use App\Models\MassSending;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        $stats = [
            'connections' => $user->whatsappConnections()->count(),
            'groups' => $user->whatsappGroups()->count(),
            'contacts' => $user->extractedContacts()->count(),
            'mass-sendings' => $user->massSendings()->count(),
        ];

        // Status de acesso do usuário
        $currentSubscription = $user->activeSubscription()->with('plan')->first();
        $accessStatus = [
            'is_admin' => $user->isAdmin(),
            'has_subscription' => $user->hasActiveSubscription(),
            'has_access' => $user->hasFeatureAccess(),
            'current_plan' => $currentSubscription ? $currentSubscription->plan : null,
        ];

        $recentConnections = $user->whatsappConnections()
            ->with('whatsappGroups')
            ->latest()
            ->take(5)
            ->get();

        $recentGroups = $user->whatsappGroups()
            ->with('whatsappConnection')
            ->latest()
            ->take(5)
            ->get();

        $recentContacts = $user->extractedContacts()
            ->with('whatsappGroup')
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard', compact('stats', 'recentConnections', 'recentGroups', 'recentContacts', 'accessStatus'));
    }
}
