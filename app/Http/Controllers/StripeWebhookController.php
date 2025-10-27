<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use App\Services\StripeService;

class StripeWebhookController extends Controller
{
    private StripeService $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    /**
     * Handle Stripe webhook events
     */
    public function handle(Request $request): Response
    {
        try {
            $payload = $request->getContent();
            $signature = $request->header('Stripe-Signature');

            if (!$signature) {
                Log::error('Missing Stripe signature header');
                return response('Missing signature', 400);
            }

            Log::info('Received Stripe webhook', [
                'signature' => $signature,
                'payload_size' => strlen($payload)
            ]);

            $this->stripeService->processWebhook($payload, $signature);

            Log::info('Stripe webhook processed successfully');
            return response('Webhook processed successfully', 200);

        } catch (\Exception $e) {
            Log::error('Stripe webhook processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response('Webhook processing failed', 400);
        }
    }
}
