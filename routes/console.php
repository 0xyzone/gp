<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Ensure Reverb WebSocket server stays running on shared hosting via cron
Schedule::command('reverb:ensure-running')
    ->everyMinute()
    ->withoutOverlapping(10);
