<?php

namespace App\SocketServer;

//require_once __DIR__ . '../../vendor/autoload.php';
//require_once __DIR__ . '/config.php';

//require_once __DIR__ . '/config.php';
//require_once __DIR__ . '/../vendor/autoload.php';

//use Logger;

use App\Http\Controllers\CharacterController;
use App\Services\CharacterService;
use App\Services\MessageService;
use App\Services\UserService;
use App\SocketServer\Contracts\DataInterface;
use App\SocketServer\Contracts\LoggerInterface;
use Workerman\Worker;

//use Server\Mysql;
//use Server\Logger;
//use Server\DataInterface;
//use Server\LoggerInterface;
use Workerman\Lib\Timer;

//use SocketServer\Logger;


class Server
{
    private $users;

//    private $db;

    private $userService;
    private $characterService;
    private $messageService;

    private $gameState;


    public function __construct(
        UserService $userService,
        CharacterService $characterService,
        MessageService $messageService,
        $config,
//        DataInterface $db,
        LoggerInterface $logger
    )
    {
        $this->ws_worker = new Worker("websocket://$config[host]:$config[port]");
//        $this->ws_worker = new Worker("websocket://192.168.215.29:$config[port]");

//        $this->db = $db;
        $this->logger           = $logger;
        $this->ws_worker->count = $config['countWorkers'];
        $this->config           = $config;

        $this->userService      = $userService;
        $this->characterService = $characterService;
        $this->messageService   = $messageService;

        $gameState = new \stdClass();
    }


    public function serverStart()
    {
        $this->users = [];
        /*
        * Стартуем сервер и пингуем БД каждую минуту, что бы сохранить подключение
        *
        */
        $this->ws_worker->onWorkerStart = function () {
            $this->logger->save(date("H:i:s"), 'Service', 'Сервер запущен');

            $time_interval = $this->config['intervalPing'];

            $timer_id = Timer::add($time_interval, function () {
//                $result = $this->db->ping();
                $result = 'пинг...';
                $this->logger->save(date("H:i:s"), 'Service', $result);
            });
        };


        /*
        * При новом подключении уведомляем пользователей, достаем старые сообщения, пишем в лог
        *
        */
        $this->ws_worker->onConnect = function ($connection/*, $data/**/) use (&$users) {

            /**/
            $debugFile = 'storage\debug1111111-onConnect.txt';
            file_exists($debugFile) ? $current = file_get_contents($debugFile) : $current = null;
            $results = print_r($users, true);
            !empty($current) ? $current .= "\r\n" . $results : $current .= "\n" . $results;
            file_put_contents($debugFile, $current);
            /**/

            $connection->onWebSocketConnect = function ($connection) use (&$users) {

                $userEmailFromClient = $_GET['user'];
                $activeCharacter     = $this->characterService->getActiveCharacterByUserEmail($userEmailFromClient);

                /**/
                $debugFile = 'storage\debug1111111--------------++++$character.txt';
                file_exists($debugFile) ? $current = file_get_contents($debugFile) : $current = null;
                $results = print_r($activeCharacter, true);
                !empty($current) ? $current .= "\r\n" . $results : $current .= "\n" . $results;
                file_put_contents($debugFile, $current);
                /**/

                /**/
//                $debugFile = 'storage\debug1111111--------------++++$connection.txt';
//                file_exists($debugFile) ? $current = file_get_contents($debugFile) : $current = null;
//                $results = print_r($connection, true);
//                !empty($current) ? $current .= "\r\n" . $results : $current .= "\n" . $results;
//                file_put_contents($debugFile, $current);
                /**/

                /**/
//                $debugFile = 'storage\debug1111111--------++++onConnect-$users.txt';
//                file_exists($debugFile) ? $current = file_get_contents($debugFile) : $current = null;
//                $results = print_r($users, true);
//                !empty($current) ? $current .= "\r\n" . $results : $current .= "\n" . $results;
//                file_put_contents($debugFile, $current);
                /**/


                /**/
                // а это сообщение будет отправлено клиенту

                $charName = !empty($activeCharacter) ? $activeCharacter->name : "Не выбран";

                $selectCharacterDialog = <<<STR
Аккaунт [{$userEmailFromClient}] Персонаж [{$charName}]
Добро пожаловать в MUD Adamant Adan!
0) Выход из AdamantAdan-MUDа.
1) Начать игру.
2) Ввести описание своего персонажа.
3) Прочитать начальную историю.
4) Поменять пароль.
5) Удалить этого персонажа.
--------------------------------
В этом аккаунте вы также можете:
6) Выбрать другого персонажа. 
7) Создать нового персонажа. 
8) Другие операции с аккаунтом.
STR;

                $service0 = json_encode(['selectCharacterDialog' => $selectCharacterDialog]);
                $connection->send($service0);


                /**/

                $this->users[$connection->id] = $connection;
//
//                if (!is_array($result)) {
//                    $this->logger->save(date("H:i:s"), 'Service', $result); // если пришел не массив - ошибка при запросе
//                } else {
//                    $result = json_encode(['dialog' => $result]);
//                    $this->users[$connection->id]->send($result);
//                }
//
                foreach ($this->users as $value) {
                    $service = json_encode(['service' => "Пользователь $userEmailFromClient присоединился."]);
                    $value->send($service);
                }

            };
        };


        /*
        * При получении сообщения записываем его в БД и рассылаем пользователям
        *
        */
        $this->ws_worker->onMessage = function ($connection, $data) use (&$users) {
            $data = json_decode($data);

            /**/
            $debugFile = 'storage\debug1111111-onMessage-$data.txt';
            file_exists($debugFile) ? $current = file_get_contents($debugFile) : $current = null;
            $results = print_r($data, true);
            !empty($current) ? $current .= "\r\n" . $results : $current .= "\n" . $results;
            file_put_contents($debugFile, $current);
            /**/

            $time       = date("H:i:s");
            $data->time = $time;

            /**/
            $debugFile = 'storage\debug1111111-onMessage-$this-users.txt';
            file_exists($debugFile) ? $current = file_get_contents($debugFile) : $current = null;
            $results = print_r($this->users, true);
            !empty($current) ? $current .= "\r\n" . $results : $current .= "\n" . $results;
            file_put_contents($debugFile, $current);
            /**/


            // ищем пользователя по уникальному ИД в базе
//            $findUser = $this->userService->findByUuid($data->uuid);

            $state = 1;
            if($state == 1){
                switch ($data->message) {
                    case 1:
                        $connection->send(json_encode(['for_client' => "\r\nПриветствуем вас на бескрайних просторах мира чудес и приключений!"]));

                        break;
//                    case 'stop':
//                        $this->stopDaemon();
//                        break;
                }
            }

            //отправить конкретному пользователю
            $connection->send(json_encode(['current_user' => time()]));

            //рассылка всем
            foreach ($this->users as $value) {

                /**/
//                $debugFile = 'storage\debug1111111-$data.txt';
//                file_exists($debugFile) ? $current = file_get_contents($debugFile) : $current = null;
//                $results = print_r($data, true);
//                !empty($current) ? $current .= "\r\n" . $results : $current .= "\n" . $results;
//                file_put_contents($debugFile, $current);
                /**/


                $value->send(json_encode($data));
            }
        };

        /*
        * При отключении уведомляем пользователей и пишем в лог
        *
        */
        $this->ws_worker->onClose = function ($connection) use (&$users) {
            $this->logger->save(date("H:i:s"), 'Service', 'Пользователь отключился');
            foreach ($this->users as $value) {
                $service = json_encode(['service' => 'Пользователь отключился.']);
                $value->send($service);
            }
        };
        Worker::runAll();
    }
}
