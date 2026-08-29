<?php

return [
    'branch' => env('GITHUB_UPDATER_BRANCH', 'main'),
    'github_token' => env('GITHUB_TOKEN'),
    'github_repo' => env('GITHUB_REPO'),

    'php_path' => env('GITHUB_UPDATER_PHP', '/usr/bin/php'),
    'composer_path' => env('GITHUB_UPDATER_COMPOSER', '/home/nexolyte/composer.phar'),

    // Seeder(s) run korবে kina — true rakhলে protibar deploy e run hobে (data reset hote pare)
    'run_seeders' => env('GITHUB_UPDATER_RUN_SEEDERS', false),
    'seeders' => [
        'RolesAndPermissionsSeeder',
        // aro seeder class add korte paro
    ],

    'commands_after_pull' => [
        'composer_install',
        'migrate',
        'seed',           // config e 'run_seeders' true thakle-i chalবে
        'config_clear',
        'config_cache',
        'route_clear',
        'route_cache',
        'view_clear',
        'view_cache',
        'cache_clear',
        'queue_restart',
    ],
];