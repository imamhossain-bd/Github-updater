<?php

namespace ImamHossain\GithubUpdater\Services;

use Symfony\Component\Process\Process;

class UpdaterService
{
    protected string $php;
    protected string $composer;

    public function __construct()
    {
        $this->php = config('github-updater.php_path', 'php');
        $this->composer = config('github-updater.composer_path', 'composer');
    }

    public function run(): array
    {
        $branch = config('github-updater.branch', 'main');
        $steps = [];

        $steps[] = $this->execute("Pull latest code (branch: {$branch})", $this->buildGitPullCommand($branch));

        foreach (config('github-updater.commands_after_pull', []) as $key) {
            $step = $this->resolveCommand($key);
            if ($step) {
                $steps[] = $this->execute($step['label'], $step['command']);
            }
        }

        return $steps;
    }

    protected function resolveCommand(string $key): ?array
    {
        return match ($key) {
            'composer_install' => [
                'label' => 'Composer install',
                'command' => "{$this->php} {$this->composer} install --optimize-autoloader --no-interaction",
            ],
            'migrate' => [
                'label' => 'Run migrations',
                'command' => "{$this->php} artisan migrate --force",
            ],
            'seed' => config('github-updater.run_seeders', false)
                ? ['label' => 'Run seeders', 'command' => $this->buildSeedCommand()]
                : null,
            'config_clear' => ['label' => 'Clear config', 'command' => "{$this->php} artisan config:clear"],
            'config_cache' => ['label' => 'Cache config', 'command' => "{$this->php} artisan config:cache"],
            'route_clear' => ['label' => 'Clear routes', 'command' => "{$this->php} artisan route:clear"],
            'route_cache' => ['label' => 'Cache routes', 'command' => "{$this->php} artisan route:cache"],
            'view_clear' => ['label' => 'Clear views', 'command' => "{$this->php} artisan view:clear"],
            'view_cache' => ['label' => 'Cache views', 'command' => "{$this->php} artisan view:cache"],
            'cache_clear' => ['label' => 'Clear application cache', 'command' => "{$this->php} artisan cache:clear"],
            'queue_restart' => ['label' => 'Restart queue workers', 'command' => "{$this->php} artisan queue:restart"],
            default => null,
        };
    }

    protected function buildSeedCommand(): string
    {
        $seeders = config('github-updater.seeders', []);

        if (empty($seeders)) {
            return "{$this->php} artisan db:seed --force";
        }

        $commands = array_map(
            fn ($seeder) => "{$this->php} artisan db:seed --class={$seeder} --force",
            $seeders
        );

        return implode(' && ', $commands);
    }

    protected function buildGitPullCommand(string $branch): string
    {
        $token = config('github-updater.github_token');
        $repo = config('github-updater.github_repo');

        return ($token && $repo)
            ? "git pull --ff-only https://{$token}@github.com/{$repo}.git {$branch}"
            : "git pull --ff-only origin {$branch}";
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

        $rawOutput = $this->stripAnsi($process->getOutput() . $process->getErrorOutput());
        $success = $process->isSuccessful();

        return [
            'label' => $label,
            'command' => $command,
            'output' => $rawOutput,
            'summary' => $this->summarize($rawOutput, $success),
            'success' => $success,
        ];
    }

    // Full raw output theke shudhu meaningful 1-line status ber kore ane
    protected function summarize(string $output, bool $success): string
    {
        $lines = array_values(array_filter(array_map('trim', explode("\n", $output)), fn($l) => $l !== ''));

        $patterns = [
            '/Nothing to migrate\.?/i',
            '/Nothing to install, update or remove\.?/i',
            '/Already up to date\.?/i',
            '/Configuration cached successfully\.?/i',
            '/Routes cached successfully\.?/i',
            '/Blade templates cached successfully\.?/i',
            '/Application cache cleared successfully\.?/i',
            '/Broadcasting queue restart signal\.?/i',
            '/Migrating:.*/i',
            '/Migrated:.*/i',
            '/Package operations:.*/i',
        ];

        foreach ($lines as $line) {
            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $line)) {
                    return $line;
                }
            }
        }

        if (!$success) {
            // Fail hole shesher meaningful line dekhায় (error hint)
            return $lines[array_key_last($lines)] ?? 'Command failed';
        }

        return 'Done';
    }

    protected function stripAnsi(string $text): string
    {
        return preg_replace('/\x1B\[[0-9;]*[a-zA-Z]/', '', $text);
    }
}