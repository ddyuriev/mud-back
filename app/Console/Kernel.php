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
        \App\Console\Commands\MyCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
//        $schedule->command('mycommand:hello-world myArgument --myOption="bla-bla-bla"')
//            ->withoutOverlapping() // не запускать задачу если ещё работает предыдущая
//            ->appendOutputTo(storage_path().'/logs/mycommand_myArgument_'.$mon.'.log')
//            ->cron('*/20 * * * *')  // каждые 20 минут
////         ->everyFiveMinutes()
//        ;
    }
}
