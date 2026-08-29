<?php

namespace ImamHossain\GithubUpdater\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class UpdateFromGithub extends Command
{
    protected $signature = 'app:update-from-github';
    protected $description = 'Pull latest code from GitHub and run post-update commands';

    public function handle()
    {
        $branch = config('github-updater.branch', 'main');

        $this->info("Pulling latest code from branch: {$branch}");
        $this->runProcess("git pull origin {$branch}");

        foreach (config('github-updater.commands_after_pull', []) as $command) {
            $this->info("Running: {$command}");
            $this->runProcess($command);
        }

        $this->info('✅ Update complete!');
    }

    protected function runProcess(string $command)
    {
        $process = Process::fromShellCommandline($command . ' --no-ansi', base_path());
        $process->setTimeout(300);
        $process->setEnv([
            'HOME' => getenv('HOME') ?: '/tmp',
            'COMPOSER_HOME' => getenv('HOME') ?: '/tmp',
        ]);

        $process->run(function ($type, $buffer) {
            $this->line($buffer);
        });

        if (!$process->isSuccessful()) {
            $this->error("❌ Command failed: {$command}");
        }
    } 
}