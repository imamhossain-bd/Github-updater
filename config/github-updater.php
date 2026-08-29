<?php

return [
    'branch' => env('GITHUB_UPDATER_BRANCH', 'main'),
    'webhook_secret' => env('GITHUB_UPDATER_SECRET'),

    'commands_after_pull' => [
        'composer install --no-dev --optimize-autoloader',
        'php artisan migrate --force',
        'php artisan config:cache',
        'php artisan route:cache',
        'php artisan view:cache',
        'php artisan queue:restart',
    ],
];