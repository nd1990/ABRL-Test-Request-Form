<?php

use App\Models\Setting;
use App\Services\BackupService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('backup:run {--reason=auto : Reason for the backup (auto|manual)}', function () {
    $this->info('Starting database backup...');

    try {
        $result = app(BackupService::class)->create($this->option('reason'));
        $this->info("Backup created: {$result['filename']} ({$result['size_human']})");
    } catch (Throwable $e) {
        $this->error('Backup failed: ' . $e->getMessage());

        return 1;
    }

    return 0;
})->purpose('Create a database backup using mysqldump');

try {
    $frequency = Setting::get('backup', 'frequency', 'off');
    $time = Setting::get('backup', 'time', '02:00');

    if ($frequency === 'daily') {
        Schedule::command('backup:run')->dailyAt($time);
    } elseif ($frequency === 'weekly') {
        Schedule::command('backup:run')->weeklyOn(1, $time);
    } elseif ($frequency === 'monthly') {
        Schedule::command('backup:run')->monthlyOn(1, $time);
    }
} catch (Throwable $e) {
    // Database unavailable — skip schedule registration so other commands still work.
}