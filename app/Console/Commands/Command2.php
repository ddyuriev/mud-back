<?php

namespace App\Console\Commands;

use App\Http\Controllers\CharacterController;
use App\SocketServer\Logger;
use App\SocketServer\MySql;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use App\SocketServer\Server;

class Command2 extends Command
{
//    protected $name = 'command2:hello-world';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mudserver:start';


    public function handle()
    {


        /**/
//        $debugFile = 'storage\debug1111111-MyCommand.txt';
//        file_exists($debugFile) ? $current = file_get_contents($debugFile) : $current = null;
//        $results = print_r([$this->argument(), $this->option()], true);
//        !empty($current) ? $current .= "\r\n" . $results : $current .= "\n" . $results;
//        file_put_contents($debugFile, $current);
        /**/


//        $config = [
//            'db' => [
//                'host' => 'localhost',
//                'user' => 'root',
//                'password' => '',
//                'dbname' => 'chat',
//                'charset' => 'utf8'
//            ],
//            'log' => [
//                'folder' => 'log',
//                'fileName' => 'LogFile.txt'
//            ],
//            'webSocket' => [
//                'host' => '127.0.0.1',
//                'port' => '8000',
//                'countWorkers' => '4',
//                'intervalPing' => 60
//            ]
//        ];

        $webSocketConfig = [
            'host' => env("WEBSOCKET_HOST"),
            'port' => env("WEBSOCKET_PORT"),
            'countWorkers' => env("WEBSOCKET_COUNTWORKERS"),
            'intervalPing' => env("WEBSOCKET_INTERVALPING")
        ];

        $dbConfig = [
            'host' => env("DB_HOST"),
            'user' => env("DB_USERNAME"),
            'password' => env("DB_PASSWORD"),
            'dbname' => env("DB_DATABASE"),
//            'charset' => env("WEBSOCKET_HOST"),
        ];
        $db     = new MySql($dbConfig);

        $logConfig = [
            'folder' => env("LOG_FOLDER"),
            'fileName' => env("LOG_FILENAME")
        ];
        $log = new Logger($logConfig);

        $server = new Server($webSocketConfig, $db, $log);
        $server->serverStart();

    }
}
