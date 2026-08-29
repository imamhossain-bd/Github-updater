<?php

return [
    'branch' => env('GITHUB_UPDATER_BRANCH', 'main'),
    'github_token' => env('GITHUB_TOKEN'),
    'github_repo' => env('GITHUB_REPO'),

    'commands_after_pull' => [
        '/usr/bin/php /home/nexolyte/composer.phar install --optimize-autoloader',
        '/usr/bin/php artisan migrate --force',
        '/usr/bin/php artisan config:cache',
        '/usr/bin/php artisan route:cache',
        '/usr/bin/php artisan view:cache',
        '/usr/bin/php artisan queue:restart',
    ],
];