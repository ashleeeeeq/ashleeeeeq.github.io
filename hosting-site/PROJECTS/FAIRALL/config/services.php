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
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
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

    'paypal' => [
        'client_id' => env('PAYPAL_CLIENT_ID'),
        'secret' => env('PAYPAL_SECRET'),
        'webhook_id' => env('PAYPAL_WEBHOOK_ID'),
        'base_url' => env('PAYPAL_BASE', 'https://api-m.sandbox.paypal.com'),
        'currency' => env('PAYMENT_CURRENCY', 'USD'),
        'subscription_plans' => [
            'support_a_scholar' => [
                'name' => 'Support a Scholar',
                'description' => 'As a Scholar Supporter, you help our Fairplay Scholars focus on school instead of child labour. Your support provides daily school lunch, transportation to/from school, uniforms and basic school supplies, academic, social and emotional support, and more.',
                'amount' => 2100,
                'currency' => 'PHP',
                'program_name' => 'Education',
                'plan_id' => 'P-91430661TF615420PNIKIXWQ',
                'web_link' => 'https://www.sandbox.paypal.com/webapps/billing/plans/subscribe?plan_id=P-91430661TF615420PNIKIXWQ',
            ],
            'support_an_intern' => [
                'name' => 'Support an Intern',
                'description' => 'As an Intern Supporter, you can help bridge the gap between school and work, providing valuable work experience for a Fairplay Scholar to level up and gain skills for better employment.',
                'amount' => 1500,
                'currency' => 'PHP',
                'program_name' => 'Education',
                'plan_id' => 'P-8C14168879822462TNIKIZNA',
                'web_link' => 'https://www.sandbox.paypal.com/webapps/billing/plans/subscribe?plan_id=P-8C14168879822462TNIKIZNA',
            ],
            'fairplay_supporters_club' => [
                'name' => 'Fairplay Supporters Club',
                'description' => 'As a Fairplay Supporter, you become part of the development of the areas of Fairplay that provide Academic, Financial, Social, and Emotional support to our community.',
                'amount' => 600,
                'currency' => 'PHP',
                'program_name' => null,
                'plan_id' => 'P-75571533FF274571XNIKIZ6I',
                'web_link' => 'https://www.sandbox.paypal.com/webapps/billing/plans/subscribe?plan_id=P-75571533FF274571XNIKIZ6I',
            ],
        ],
    ],

    'xendit' => [
        'key' => env('XENDIT_KEY'),
        'callback_token' => env('XENDIT_CALLBACK_TOKEN'),
        'base_url' => env('XENDIT_BASE', 'https://api.xendit.co'),
        'currency' => env('PAYMENT_CURRENCY', 'USD'),
    ],

    'groq' => [
        'api_key' => env('GROQ_API_KEY'),
        'base_url' => env('GROQ_BASE', 'https://api.groq.com/openai/v1'),
        'model' => env('GROQ_MODEL', 'meta-llama/llama-4-scout-17b-16e-instruct,llama-3.1-8b-instant'),
        'timeout' => env('GROQ_TIMEOUT', 120),
        'temperature' => env('GROQ_TEMPERATURE', 0.3),
        'max_tokens' => env('GROQ_MAX_TOKENS', 2048),
        'frequency_penalty' => env('GROQ_FREQUENCY_PENALTY', 0.5),
        'presence_penalty' => env('GROQ_PRESENCE_PENALTY', 0.3),
        'top_p' => env('GROQ_TOP_P', 0.9),
        'cache_ttl' => env('GROQ_CACHE_TTL', 86400),
        'retry_attempts' => env('GROQ_RETRY_ATTEMPTS', 2),
        'batch_retry_attempts' => env('GROQ_BATCH_RETRY_ATTEMPTS', 1),
        'retry_delay_ms' => env('GROQ_RETRY_DELAY_MS', 1500),
        'pool_chunk' => env('GROQ_POOL_CHUNK', 5),
        'fallback_cooldown_ms' => env('GROQ_FALLBACK_COOLDOWN_MS', 3000),
        'reasoning_format' => env('GROQ_REASONING_FORMAT'),
        'include_reasoning' => env('GROQ_INCLUDE_REASONING'),
        'reasoning_effort' => env('GROQ_REASONING_EFFORT'),
    ],

    'gotenberg' => [
        'url' => env('GOTENBERG_URL', 'http://localhost:3000'),
        'username' => env('GOTENBERG_USERNAME'),
        'password' => env('GOTENBERG_PASSWORD'),
        'timeout' => env('GOTENBERG_TIMEOUT', 60),
    ],

    'google_cloud_vision' => [
        'api_key' => env('GOOGLE_VISION_API_KEY'),
        'timeout' => env('GOOGLE_VISION_TIMEOUT', 30),
    ],

    'turnstile' => [
        'site_key' => env('TURNSTILE_SITE_KEY'),
        'secret_key' => env('TURNSTILE_SECRET_KEY'),
    ],

    'fcm' => [
        'server_key' => env('FCM_SERVER_KEY'),
        'credentials' => env('FIREBASE_CREDENTIALS', storage_path('app/firebase/service-account.json')),
    ],

];
