<?php

namespace App\Console\Commands;

use App\Http\Controllers\CharacterController;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use SocketServer\Logger;
//use SocketServer\Server;
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

    /**/

    public function handle()
    {


//        $users = User::All();

        /**/
//        $debugFile = 'storage\debug1111111-MyCommand.txt';
//        file_exists($debugFile) ? $current = file_get_contents($debugFile) : $current = null;
//        $results = print_r([$this->argument(), $this->option()], true);
//        !empty($current) ? $current .= "\r\n" . $results : $current .= "\n" . $results;
//        file_put_contents($debugFile, $current);
        /**/

        /**/
//        $debugFile = 'storage\debug1111111-$users.txt';
//        file_exists($debugFile) ? $current = file_get_contents($debugFile) : $current = null;
//        $results = print_r($users, true);
//        !empty($current) ? $current .= "\r\n" . $results : $current .= "\n" . $results;
//        file_put_contents($debugFile, $current);
        /**/


        $config = [
            'db'        => [
                'host'     => 'localhost',
                'user'     => 'root',
                'password' => '',
                'dbname'   => 'chat',
                'charset'  => 'utf8'
            ],
            'log'       => [
                'folder'   => 'log',
                'fileName' => 'LogFile.txt'
            ],
            'webSocket' => [
                'host'         => '127.0.0.1',
                'port'         => '8000',
                'countWorkers' => '4',
                'intervalPing' => 60
            ]
        ];

//        $log    = new Logger($config['log']);

        $log = 1;

        $server = new Server($config['webSocket'], /*$db,*/
            $log);
        $server->serverStart();


//        $characterController = new CharacterController();
//        $characterController->index();
    }

    /**/
}