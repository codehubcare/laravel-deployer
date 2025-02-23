<?php

return [
    /*
    |--------------------------------------------------------------------------
    | GitHub Authentication Token
    |--------------------------------------------------------------------------
    |
    | This token will be used to authenticate with the GitHub API.
    | You can generate a token at https://github.com/settings/tokens
    |
    */
    'github_token' => env('GITHUB_TOKEN', ''),

    'src_path' => env('LARAVEL_DEPLOYER_SRC_PATH', 'src'),
    'public_path' => env('LARAVEL_DEPLOYER_PUBLIC_PATH', 'public_html'),

    'ftp' => [
        'host' => env('LARAVEL_DEPLOYER_FTP_HOST', ''),
        'username' => env('LARAVEL_DEPLOYER_FTP_USERNAME', ''),
        'password' => env('LARAVEL_DEPLOYER_FTP_PASSWORD', ''),
        'port' => env('LARAVEL_DEPLOYER_FTP_PORT', 21),
    ],
];
