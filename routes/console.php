<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Scheduling
Schedule::command('app:send-invoice-reminders')
    ->dailyAt('08:00')
    ->timezone('Africa/Johannesburg');

Schedule::command('app:send-queued-emails')
    ->dailyAt('08:05')
    ->timezone('Africa/Johannesburg');
