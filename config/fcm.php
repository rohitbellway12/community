<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Firebase Cloud Messaging (FCM) Configuration
    |--------------------------------------------------------------------------
    |
    | Credentials for sending push notifications to mobile devices.
    | Uses the FCM HTTP v1 API.
    |
    */

    'project_id'       => env('FCM_PROJECT_ID'),
    'credentials_json' => env('FCM_CREDENTIALS_JSON'),

    /*
    |--------------------------------------------------------------------------
    | Legacy Server Key (optional fallback)
    |--------------------------------------------------------------------------
    */

    'server_key' => env('FCM_SERVER_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Default notification options
    |--------------------------------------------------------------------------
    */

    'default_title' => env('FCM_DEFAULT_TITLE', 'Community App'),
    'default_icon'  => env('FCM_DEFAULT_ICON', 'notification_icon'),
    'default_color' => env('FCM_DEFAULT_COLOR', '#0D8ABC'),
];
