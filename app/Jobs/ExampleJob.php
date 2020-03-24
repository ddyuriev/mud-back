<?php

namespace App\Jobs;

use App\Helpers\Debugger;
use Faker\Factory;
use Faker\Generator;

class ExampleJob extends Job
{
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
//        sleep(3);
        /**/
        Debugger::PrintToFile('----ExampleJo--' . time(), 'ExampleJob');
        /**/
//        sleep(3);

        $faker = Factory::create();

        for ($i= 0; $i <= 2000000; $i++){
                $str = 'sdfsdfsfsf';
//                $faker = Factory::create();
                $k = $faker->randomFloat();
        }
        /**/
        Debugger::PrintToFile('----ExampleJo--' . time(), 'ExampleJob');
        /**/
    }
}
