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

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'toyyibpay' => [
        'rizqmall' => [
            'secret' => env('TOYYIBPAY_RIZQMALL_SECRET'),
            'category' => env('TOYYIBPAY_RIZQMALL_CATEGORY'),
            'url' => env('TOYYIBPAY_RIZQMALL_URL', 'https://dev.toyyibpay.com'),
        ],
        'sandbox' => [
            'secret' => env('TOYYIBPAY_SANDBOX_SECRET'),
            'category' => env('TOYYIBPAY_SANDBOX_CATEGORY'),
            'url' => env('TOYYIBPAY_SANDBOX_URL', 'https://toyyibpay.com'),
        ],
    ],


    'rizqmall' => [
        'base_url' => env('RIZQMALL_BASE_URL', 'http://rizqmall.test'),
        'api_key' => env('RIZQMALL_API_KEY', 'your-secret-api-key-here'),
        'sso_secret' => env('RIZQMALL_SSO_SECRET', env('APP_KEY')),
        'webhook_secret' => env('RIZQMALL_WEBHOOK_SECRET'),
    ],


];
