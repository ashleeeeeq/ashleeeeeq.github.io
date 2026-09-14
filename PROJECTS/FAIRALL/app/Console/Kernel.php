<?php
namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('app:ping-gotenberg')->everyTenMinutes();
        $schedule->command('app:check-subscription-renewals')->dailyAt('00:00');
        $schedule->command('app:check-beneficiary-status-endings')->dailyAt('06:00');
        $schedule->command('dashboard:refresh-metrics')->hourly();
        $schedule->command('queue:work --max-time=55 --max-jobs=10')
            ->everyMinute()
            ->withoutOverlapping();
    }

    protected function commands(): void
    {
        //
    }
}
