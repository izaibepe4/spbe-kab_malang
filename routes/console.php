<?php

use App\Services\ApiHealthMonitor;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Health Check Monitoring (every 5 minutes)
Schedule::call(function () {
    if (! config('monitoring.health_check.enabled')) {
        return;
    }

    $monitor = new ApiHealthMonitor;
    $endpoints = config('monitoring.api_endpoints', []);

    foreach ($endpoints as $name => $url) {
        $monitor->checkHealth($name, $url);
    }
})->everyFiveMinutes()->name('health-check')->withoutOverlapping();

// Clean old logs (daily at 2 AM)
Schedule::command('cache:prune-stale-tags')->daily()->at('02:00');

// Queue monitoring (if using queues)
if (config('queue.default') !== 'sync') {
    Schedule::command('queue:monitor')->everyMinute();
}

// Automated backup (daily at 2 AM) - Spatie Laravel Backup
Schedule::command('backup:run --only-db')
    ->daily()
    ->at('02:00')
    ->name('database-backup')
    ->withoutOverlapping();

// Clean old backups (daily at 3 AM)
Schedule::command('backup:clean')
    ->daily()
    ->at('03:00')
    ->name('backup-cleanup')
    ->withoutOverlapping();
