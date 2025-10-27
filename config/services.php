<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'wuzapi' => [
        'base_url' => env('WUZAPI_BASE_URL', 'https://api.wuzapi.com'),
        'token' => env('WUZAPI_TOKEN'),
        'admin_token' => env('WUZAPI_ADMIN_TOKEN'),
    ],

    'bestfy' => [
        'base_url' => env('BESTFY_BASE_URL', 'https://api.bestfybr.com.br/v1'),
        'secret_key' => env('BESTFY_SECRET_KEY'),
        'public_key' => env('BESTFY_PUBLIC_KEY'),
    ],

    'stripe' => [
        'secret_key' => env('STRIPE_SECRET_KEY'),
        'public_key' => env('STRIPE_PUBLIC_KEY'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
        'sandbox_secret_key' => env('STRIPE_SANDBOX_SECRET_KEY'),
        'sandbox_public_key' => env('STRIPE_SANDBOX_PUBLIC_KEY'),
        'sandbox_webhook_secret' => env('STRIPE_SANDBOX_WEBHOOK_SECRET'),
        'sandbox_id' => env('STRIPE_SANDBOX_ID'),
        'mode' => env('STRIPE_MODE', 'sandbox'), // 'sandbox' or 'live'
    ],

];