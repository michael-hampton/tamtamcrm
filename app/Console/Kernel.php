<?php

namespace App\Console;

use App\Console\Commands\SendRecurring;
use App\Jobs\Invoice\ProcessReminders;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        'App\Console\Commands\SendRecurring'
    ];

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //$schedule->job(new RecurringInvoicesCron)->hourly();
        $schedule->command(SendRecurring::class)->hourly();
        $schedule->command('horizon:snapshot')->everyFiveMinutes();
        $schedule->json(new ProcessReminders)->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
