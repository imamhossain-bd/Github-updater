<?php

namespace ImamHossain\GithubUpdater;

use Illuminate\Support\ServiceProvider;
use ImamHossain\GithubUpdater\Console\Commands\UpdateFromGithub;

class GithubUpdaterServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/github-updater.php', 'github-updater');
    }

    public function boot()
    {
        $this->commands([
            UpdateFromGithub::class,
        ]);

        $this->publishes([
            __DIR__.'/../config/github-updater.php' => config_path('github-updater.php'),
        ], 'config');

        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
    }
}