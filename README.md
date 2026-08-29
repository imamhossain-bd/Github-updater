# GitHub Updater for Laravel

Deploy your Laravel app with one click — pull latest code from GitHub, install packages, run migrations, and clear caches, all automatically.

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

## Usage

Visit this URL to deploy:  https://yourdomain.com/github-pull



That's it — it will pull the code, install dependencies, run migrations, and clear all caches automatically.

## What it does

1. Pulls latest code from GitHub
2. Runs `composer install`
3. Runs migrations
4. Runs seeders (if enabled)
5. Clears and rebuilds cache
6. Restarts queue

## License

MIT