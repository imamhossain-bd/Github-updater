<?php

namespace ImamHossain\GithubUpdater\Services;

use Symfony\Component\Process\Process;

class UpdaterService
{
    public function run(): array
    {
        $branch = config('github-updater.branch', 'main');
        $steps = [];

        $steps[] = $this->execute("Pull latest code (branch: {$branch})", "git pull origin {$branch}");

        foreach (config('github-updater.commands_after_pull', []) as $command) {
            $steps[] = $this->execute($command, $command);
        }

        return $steps;
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