<?php

namespace App\Console\Commands;

use App\Http\Controllers\CharacterController;
use App\Services\MessageService;
use App\Services\UserService;
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
        $webSocketConfig = [
            'host' => env("WEBSOCKET_HOST"),
            'port' => env("WEBSOCKET_PORT"),
            'countWorkers' => env("WEBSOCKET_COUNTWORKERS"),
            'intervalPing' => env("WEBSOCKET_INTERVALPING")
        ];

//        $dbConfig = [
//            'host' => env("DB_HOST"),
//            'user' => env("DB_USERNAME"),
//            'password' => env("DB_PASSWORD"),
//            'dbname' => env("DB_DATABASE"),
////            'charset' => env("WEBSOCKET_HOST"),
//        ];
//        $db = new MySql($dbConfig);

        $logConfig = [
            'folder' => env("LOG_FOLDER"),
            'fileName' => env("LOG_FILENAME")
        ];
        $log = new Logger($logConfig);

//        $server = app(Server::class);
//        $server = new Server(new UserService(), $webSocketConfig, $db, $log);
        $server = new Server(new UserService(), new MessageService(), $webSocketConfig, /*$db,*/ $log);

        $server->serverStart();

    }
}
