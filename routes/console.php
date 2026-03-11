<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Scheduled Reports for Stakeholder Coordination
Schedule::command('reports:generate --type=daily --email=admin@nulipa.edu.ph')
    ->dailyAt('08:00')
    ->description('Generate daily performance reports');

Schedule::command('reports:generate --type=weekly --email=admin@nulipa.edu.ph --email=registrar@nulipa.edu.ph')
    ->weeklyOn(1, '09:00') // Every Monday at 9 AM
    ->description('Generate weekly analytics reports');

Schedule::command('reports:generate --type=monthly --email=admin@nulipa.edu.ph --email=pia@nulipa.edu.ph')
    ->monthlyOn(1, '10:00') // 1st of every month at 10 AM
    ->description('Generate monthly comprehensive reports');

Schedule::command('reports:generate --type=compliance --email=pia@nulipa.edu.ph --email=compliance@nulipa.edu.ph')
    ->weeklyOn(5, '16:00') // Every Friday at 4 PM
    ->description('Generate PIA compliance reports');