<?php

namespace ImamHossain\GithubUpdater\Services;

use Symfony\Component\Process\Process;

class UpdaterService
{
    public function run(): array
    {
        $branch = config('github-updater.branch', 'main');
        $steps = [];

        $steps[] = $this->execute("Pull latest code (branch: {$branch})", $this->buildGitPullCommand($branch));

        foreach (config('github-updater.commands_after_pull', []) as $command) {
            $steps[] = $this->execute($command, $command);
        }

        return $steps;
    }

    protected function buildGitPullCommand(string $branch): string
    {
        $token = config('github-updater.github_token');
        $repo = config('github-updater.github_repo');

        if ($token && $repo) {
            // Token diye authenticated pull URL banano hocche
            return "git pull https://{$token}@github.com/{$repo}.git {$branch}";
        }

        // Token na thakle normal pull (SSH key set thakle eta chalবে)
        return "git pull origin {$branch}";
    }

    protected function execute(string $label, string $command): array
    {
        $process = Process::fromShellCommandline($command, base_path());
        $process->setTimeout(300);
        $process->setEnv([
            'HOME' => getenv('HOME') ?: '/tmp',
            'COMPOSER_HOME' => getenv('HOME') ?: '/tmp',
            'NO_COLOR' => '1',
        ]);
        $process->run();

        return [
            'label' => $label,
            'command' => $command,
            'output' => $this->stripAnsi($process->getOutput() . $process->getErrorOutput()),
            'success' => $process->isSuccessful(),
        ];
    }

    protected function stripAnsi(string $text): string
    {
        return preg_replace('/\x1B\[[0-9;]*[a-zA-Z]/', '', $text);
    }
}