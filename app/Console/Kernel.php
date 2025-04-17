<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // 거래 상태 변경
        $schedule->command('auto-cancel')->everyFiveMinutes();
        // 통계 데이터
        $schedule->command('save-completed-internal-transactions')->dailyAt('03:00');
        $schedule->command('save-total-internal-transaction-counts')->dailyAt('03:30');
        $schedule->command('save-regist-user-count')->dailyAt('04:00');
        $schedule->command('save-current-wallet-balance')->dailyAt('04:30');

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
