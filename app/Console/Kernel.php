<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\ControlPanelToShipStation::class,
        Commands\ControlPanelToShippingEasy::class,
        Commands\Test::class,
        Commands\Setup::class,
        Commands\AddRecordToSE::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('cron:process-cp-to-ss')->withoutOverlapping()->everyFifteenMinutes();
        $schedule->command('cron:process-cp-to-se')->withoutOverlapping()->everyFifteenMinutes();
    }
}
