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

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'google' => [
    'client_id' => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'redirect' => env('GOOGLE_REDIRECT_URI'),
],
    'telebirr' => [
    'app_id'      => env('TELEBIRR_APP_ID'),
    'app_key'     => env('TELEBIRR_APP_KEY'),
    'short_code'  => env('TELEBIRR_SHORT_CODE'),
    'public_key'  => env('TELEBIRR_PUBLIC_KEY'),
    'base_url'    => env('TELEBIRR_BASE_URL'),
    'notify_url'  => env('TELEBIRR_NOTIFY_URL'),
    'return_url'  => env('TELEBIRR_RETURN_URL'),
],
    
    'fayda' => [
    'client_id'     => env('FAYDA_CLIENT_ID'),
    'client_secret' => env('FAYDA_CLIENT_SECRET'),
    'redirect_uri'  => env('FAYDA_REDIRECT_URI'),
    'base_url'      => env('FAYDA_BASE_URL'),
],
    

];
