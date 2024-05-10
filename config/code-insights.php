<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Laravel Code Insights Route Path
    |--------------------------------------------------------------------------
    |
    | This is the URI path where code insights will be accessible from.
    | You change this path to anything you like.
    |
    */

    'path' => env('CODE_INSIGHTS_PATH', 'code-insights'),
    'public' => [
        'folder' => 'laravel-code-insights'
    ],

    /*
    |--------------------------------------------------------------------------
    | Define Namespaces
    |--------------------------------------------------------------------------
    |
    | The following array lists the "namespaces" that will be registered with
    | laravel code insights.Feel free to customize those namespaces.
    |
    */
    'namespaces' => [
        'controllers' => 'App\\Http\\Controllers\\',
        'models' => 'App\\Models\\',
        'helpers' => 'App\\Http\\Helpers\\',
        'repositories' => 'App\\Repositories\\',
        'services' => 'App\\Services\\',
    ]
];
