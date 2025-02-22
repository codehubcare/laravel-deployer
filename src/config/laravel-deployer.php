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

    'src_path' => '',
    'public_path' => '',

    'ftp' => [
        'host' => env('FTP_HOST', ''),
        'username' => env('FTP_USERNAME', ''),
        'password' => env('FTP_PASSWORD', ''),
        'port' => env('FTP_PORT', 21),
    ],
];
