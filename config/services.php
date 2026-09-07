<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | Here you may configure the third party services used by your application.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'bale' => [
        'bot_token' => env('BALE_BOT_TOKEN'),
        'bot_username' => env('BALE_BOT_USERNAME'),

        // در هاست اشتراکی public_html کنار پوشه Laravel قرار دارد.
        // در صورت تفاوت ساختار مسیر، مقدار PUBLIC_HTML_PATH را در .env تنظیم کنید.
        'public_path' => env('PUBLIC_HTML_PATH', base_path('../public_html')),
    ],

];
