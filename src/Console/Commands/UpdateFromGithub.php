<?php

namespace ImamHossain\GithubUpdater\Console\Commands;

use Illuminate\Console\Command;
use ImamHossain\GithubUpdater\Services\UpdaterService;

class UpdateFromGithub extends Command
{
    protected $signature = 'app:update-from-github';
    protected $description = 'Pull latest code from GitHub and run post-update commands';

    public function handle(UpdaterService $service)
    {
        foreach ($service->run() as $step) {
            $step['success'] ? $this->info("✔ {$step['label']}") : $this->error("✘ {$step['label']}");
        }

        $this->info('Update complete!');
    }
}