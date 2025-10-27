<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Display the user's profile.
     */
    public function index()
    {
        $user = auth()->user();
        $user->load(['activeSubscription.plan', 'subscriptions.plan']);
        
        return view('profile.index', compact('user'));
    }

    /**
     * Regenerate the user's API token.
     */
    public function regenerateToken(Request $request)
    {
        $user = auth()->user();
        $token = $user->generateApiToken();

        // Se for requisição AJAX, retornar JSON
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Token regenerado com sucesso!',
                'newToken' => $token
            ]);
        }

        return back()->with('success', 'Token regenerado com sucesso!')->with('newToken', $token);
    }
}
