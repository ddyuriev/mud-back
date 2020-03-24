<?php

namespace App\Jobs;

use App;
use App\Helpers\Debugger;
use Faker\Factory;
use Exception;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;


class SaveCharacter extends Job implements ShouldQueue
{
//    use InteractsWithQueue, SerializesModels;


    public function __construct()
    {
    }

    /**
     *
     */
    public function handle()
    {
//        sleep(3);
        /**/
        Debugger::PrintToFile('----SaveCharacter--' . time(), 'ExampleJob');
        /**/
//        sleep(3);

        $faker = Factory::create();

        for ($i = 0; $i <= 2000000; $i++) {
            $str = 'sdfsdfsfsf';
//                $faker = Factory::create();
            $k = $faker->randomFloat();
        }
        /**/
        Debugger::PrintToFile('----SaveCharacter--' . time(), 'ExampleJob');
        /**/
    }
}
