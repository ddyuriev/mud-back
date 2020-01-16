<?php

namespace App\Console\Commands;


//use MoyKassir\TabletEvents\Worker;
use Illuminate\Console\Command;
//use QXS\WorkerPool\WorkerPool;
use App\Http\Controllers\TimeController;
use Carbon\Carbon;

/**
 * Class EventDaemon
 *
 * Запись чеков в монгу
 * Может работать в несколько потоков.
 *
 * @package MoyKassir\Console\Commands
 */
class EventDaemon/* extends Command*/
{
//    /**
//     * The name and signature of the console command.
//     *
//     * @var string
//     */
//    protected $signature = 'EventDaemon {action}';
//
//    /**
//     * The console command description.
//     *
//     * @var string
//     */
//    protected $description = 'Start/Stop daemons';
//
//
//    protected $wp;
//
//    protected $workersStartTime;
//
//    protected $workersRestartPeriod = 300; // sec
//
//
//    /**
//     * Create a new command instance.
//     *
//     */
//    public function __construct()
//    {
//        parent::__construct();
//    }

//    /**
//     * Execute the console command.
//     *
//     * @return mixed
//     */
//    public function handle()
//    {
//        $action = $this->argument('action');
//
//        if (!in_array($action, ['start', 'stop'])) {
//            $this->error('Action can be start or stop!');
//            die;
//        }
//        switch ($action) {
//            case 'start':
//                $this->startDaemon();
//                break;
//
//            case 'stop':
//                $this->stopDaemon();
//                break;
//        }
//    }
//
//
//    protected function startWorkers()
//    {
//        $this->wp = new WorkerPool();
//        $this->wp->setWorkerPoolSize(env('EVENT_WORKERS'))->create(new Worker());
//
//        for ($i = 0; $i < env('EVENT_WORKERS'); $i++) {
//            $this->wp->run(null);
//        }
//
//        $this->workersStartTime = time();
//    }
//
//    protected function startDaemon()
//    {
//        $this->startWorkers();
//
//        while (true) {
//            // Check every 5 seconds
//            $workersCount  = $this->wp->getFreeAndBusyWorkers();
//            $restartNeeded = time() - $this->workersStartTime > $this->workersRestartPeriod;
//
//            // If not all workers are alive or restart is needed
//            if ($workersCount['total'] < env('EVENT_WORKERS') || $restartNeeded) {
//                // Restart workers pool
//                $this->wp->destroy();
//                $this->startWorkers();
//            }
//
//            sleep(1);
//        }
//    }
//
//    protected function stopDaemon()
//    {
//        global $argv;
//
//        $filename      = $argv[0];
//        $processes     = explode("\n", trim(`pgrep -l -f $filename`));
//        $processesPids = $shPids = [];
//
//        foreach ($processes as $process) {
//            preg_match('/^(\d+) (' . preg_quote($filename) . '*+|php$)/', $process, $matches);
//
//            if (isset($matches[1]) && isset($matches[2]) && $matches[1] != getmypid()) {
//                $processesPids[] = $matches[1];
//            }
//        }
//
//        if (count($processesPids)) {
//            sort($processesPids);
//
//            // EventDaemon spawned before workers and has lowest PID
//            $eventdaemonPid = (int)array_shift($processesPids);
//
//            // SIGKILL daemon process (instant termination)
//            if (posix_kill($eventdaemonPid, SIGKILL)) {
//                echo 'Event daemon stopped' . PHP_EOL;
//            } else {
//                echo 'Event daemon stop failed' . PHP_EOL;
//            }
//
//            foreach ($processesPids as $key => $workerPid) {
//                // SIGTERM worker process (graceful termination)
//                if (posix_kill($workerPid, SIGTERM)) {
//                    echo 'Stopping event worker ' . ($key + 1) . ' of ' . count($processesPids) . '...' . PHP_EOL;
//                }
//            }
//        }
//    }


//$action = $this->argument('action');


    public function __construct()
    {
        $this->localsocket = 'tcp://127.0.0.1:1234';
        $this->user        = 'tester01';
        $this->message     = 'test';
    }


    /*protected*/
    function startDaemon()
    {
//        $this->startWorkers();

        $aux = 0;

        while (true) {
//            // Check every 5 seconds
//            $workersCount  = $this->wp->getFreeAndBusyWorkers();
//            $restartNeeded = time() - $this->workersStartTime > $this->workersRestartPeriod;
//
//            // If not all workers are alive or restart is needed
//            if ($workersCount['total'] < env('EVENT_WORKERS') || $restartNeeded) {
//                // Restart workers pool
//                $this->wp->destroy();
//                $this->startWorkers();
//            }

//            $time = TimeController::BIRTH_DAY - time();
            //UTC
//            $time = time() - 1577232000;
            //MSK
            $time = time() - 1577221200;

            if ($time % 60 == 0) {

                if ($aux == $time / 60) {
                    continue;
                }

                $aux = $time / 60;


                $mt = microtime(true);

                /**/
                $debugFile = "logs/debug1111111-$aux.$mt.txt";
                file_exists($debugFile) ? $current = file_get_contents($debugFile) : $current = null;
                $results = print_r($aux, true);
                !empty($current) ? $current .= "\r\n" . $results : $current .= "\n" . $results;
                file_put_contents($debugFile, $current);
                /**/

//                $parsedTime = Carbon::createFromTimestamp($time)->toDateTimeString();

                //дней
                $daysLeft = (int)($time / 86400);
                //часов
                $hoursLeft = (int)(($time - $daysLeft * 86400) / 3600);
                //минут
                $minutesLeft = (int)(($time - $daysLeft * 86400 - $hoursLeft * 3600) / 60);


                $message = 'Время жизни вселенной: ';
                $message .= $daysLeft . ' дней, ';
                $message .= $hoursLeft . ' часов, ';
                $message .= $minutesLeft . ' минут';

                /**/
//                $debugFile = "logs/debug1111111-$mt.txt";
//                file_exists($debugFile) ? $current = file_get_contents($debugFile) : $current = null;
//                $results = print_r($parsedDays, true);
//                !empty($current) ? $current .= "\r\n" . $results : $current .= "\n" . $results;
//                file_put_contents($debugFile, $current);
                /**/


                // соединяемся с локальным tcp-сервером
                $instance = stream_socket_client($this->localsocket);
                // отправляем сообщение
                fwrite($instance, json_encode(['user' => 'tester01', 'message' => $message]) . "\n");
            }

            //задержка в микро секундах
            usleep(100000);
        }
    }
}

$eventDaemon = new EventDaemon();
$eventDaemon->startDaemon();

