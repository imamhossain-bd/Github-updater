# GitHub Updater for Laravel

Deploy your Laravel app with one click — pull latest code from GitHub, install packages, run migrations, and clear caches, all automatically. Requires login to use.

## Installation

```bash
composer require imamhossain/github-updater
```

## Setup

Publish the config file:

```bash
php artisan vendor:publish --provider="ImamHossain\GithubUpdater\GithubUpdaterServiceProvider" --tag=config
```

Add these to your `.env`:

```env
GITHUB_UPDATER_BRANCH=main
GITHUB_TOKEN=your_github_token
GITHUB_REPO=your-username/your-repo-name
GITHUB_UPDATER_PHP=/usr/bin/php
GITHUB_UPDATER_COMPOSER=/path/to/composer.phar
GITHUB_UPDATER_RUN_SEEDERS=false
```

**GitHub Token:** GitHub → Settings → Developer settings → Personal access tokens (classic) → Generate new token → select `repo` scope.

**PHP Path:** On shared hosting, make sure `GITHUB_UPDATER_PHP` points to the CLI version of PHP, not CGI. Check with:
```bash
find / -path "*/bin/php" -type f 2>/dev/null
```
Test each path with `/path/to/php -i | grep "Server API"` — pick the one that says **Command Line Interface**.

## Usage

Log in to your app, then visit:   https://yourdomain.com/github-pull



You'll see a live console showing each step as it runs. If you're not logged in, you'll get an "Access Denied" message.

## What it does

1. Pulls latest code from GitHub
2. Runs `composer install`
3. Runs migrations
4. Runs seeders (if `GITHUB_UPDATER_RUN_SEEDERS=true`)
5. Clears and rebuilds cache
6. Restarts queue

## Requirements

- PHP >= 8.1
- Laravel 9, 10, 11, or 12
- Git installed on the server

## License

MIT