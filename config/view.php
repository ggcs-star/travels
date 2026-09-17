<?php

return [

    /*
    |--------------------------------------------------------------------------
    | View Storage Paths
    |--------------------------------------------------------------------------
    */

    'paths' => [
        resource_path('views'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Compiled View Path
    |--------------------------------------------------------------------------
    |
    | IMPORTANT:
    | Do not use realpath() here. The directory may not exist yet on a fresh
    | installation, which makes realpath() return false and causes Laravel's
    | Blade compiler to throw "Please provide a valid cache path."
    |
    */

    'compiled' => env(
        'VIEW_COMPILED_PATH',
        storage_path('framework/views')
    ),

];
