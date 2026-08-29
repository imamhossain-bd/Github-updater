<?php

return [
    'branch' => env('GITHUB_UPDATER_BRANCH', 'main'),
    'github_token' => env('GITHUB_TOKEN'),
    'github_repo' => env('GITHUB_REPO'),

    'commands_after_pull' => [
        'composer install --optimize-autoloader',
        'php artisan migrate --force',
        'php artisan config:cache',
        'php artisan route:cache',
        'php artisan view:cache',
        'php artisan queue:restart',
    ],
];