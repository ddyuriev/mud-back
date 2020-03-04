<?php

namespace App\SocketServer;

//require_once __DIR__ . '../../vendor/autoload.php';
//require_once __DIR__ . '/config.php';

//require_once __DIR__ . '/config.php';
//require_once __DIR__ . '/../vendor/autoload.php';

//use Logger;

use App\Http\Controllers\CharacterController;
use App\Room;
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
//    private $users;
    private $connections;

//    private $db;

    private $userService;

    private $characterService;

    private $messageService;

//    private $gameState;

    private $characters;


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

//        $gameState = new \stdClass();

        $characters = [];
    }


    public function serverStart()
    {
        //грузим зоны
        $roomsArray = Room::all()->toArray();

        foreach ($roomsArray as $roomArray) {
            $rooms[$roomArray['uuid']] = $roomArray;
        }

        /**/
        $debugFile = 'storage\debug1111111-$rooms.txt';
        file_exists($debugFile) ? $current = file_get_contents($debugFile) : $current = null;
        $results = print_r($rooms, true);
        !empty($current) ? $current .= "\r\n" . $results : $current .= "\n" . $results;
        file_put_contents($debugFile, $current);
        /**/


//        $this->users = [];
        $this->connections = [];
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
//            $debugFile = 'storage\debug1111111-onConnect.txt';
//            file_exists($debugFile) ? $current = file_get_contents($debugFile) : $current = null;
//            $results = print_r($users, true);
//            !empty($current) ? $current .= "\r\n" . $results : $current .= "\n" . $results;
//            file_put_contents($debugFile, $current);
            /**/

            $connection->onWebSocketConnect = function ($connection) use (&$users) {

                $userEmailFromClient = $_GET['user'];
                $activeCharacter     = $this->characterService->getActiveCharacterByUserEmail($userEmailFromClient);

                $this->characters[$activeCharacter->user->uuid] = $activeCharacter;

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
<span>
Аккaунт [{$userEmailFromClient}] Персонаж [{$charName}]<br>
Добро пожаловать в MUD Adamant Adan!<br>
0) Выход из AdamantAdan-MUDа.<br>
1) Начать игру.<br>
2) Ввести описание своего персонажа.<br>
3) Прочитать начальную историю.<br>
4) Поменять пароль.<br>
5) Удалить этого персонажа.<br>
--------------------------------<br>
В этом аккаунте вы также можете:<br>
6) Выбрать другого персонажа.<br>
7) Создать нового персонажа.<br>
8) Другие операции с аккаунтом.
</span>
STR;

//                $selectCharacterDialog = <<<STR
//<span>
//</span>
//STR;

                $service0 = json_encode(['selectCharacterDialog' => $selectCharacterDialog]);

                /**/
//                $debugFile = 'storage\debug1111111--------------++++$service0.txt';
//                file_exists($debugFile) ? $current = file_get_contents($debugFile) : $current = null;
//                $results = print_r($service0, true);
//                !empty($current) ? $current .= "\r\n" . $results : $current .= "\n" . $results;
//                file_put_contents($debugFile, $current);
                /**/

                $connection->send($service0);


                /**/

//                $this->users[$connection->id] = $connection;
                $this->connections[$connection->id] = $connection;
//
//                if (!is_array($result)) {
//                    $this->logger->save(date("H:i:s"), 'Service', $result); // если пришел не массив - ошибка при запросе
//                } else {
//                    $result = json_encode(['dialog' => $result]);
//                    $this->users[$connection->id]->send($result);
//                }
//
                foreach ($this->connections as $value) {
                    $service = json_encode(['service' => "Пользователь $userEmailFromClient присоединился."]);
                    $value->send($service);
                }

            };
        };


        /*
        * При получении сообщения записываем его в БД и рассылаем пользователям
        *
        */
        $this->ws_worker->onMessage = function ($connection, $data) use (&$users, $rooms) {
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
            $debugFile = 'storage\debug1111111-onMessage-$this-connections.txt';
            file_exists($debugFile) ? $current = file_get_contents($debugFile) : $current = null;
            $results = print_r($this->connections, true);
            !empty($current) ? $current .= "\r\n" . $results : $current .= "\n" . $results;
            file_put_contents($debugFile, $current);
            /**/


            $dataUserUuid = $data->uuid;

            // ищем пользователя по уникальному ИД в базе
//            $findUser = $this->userService->findByUuid($data->uuid);

            //начало
            $state = !empty($this->characters[$dataUserUuid]['state']) ? $this->characters[$dataUserUuid]['state'] : 1;
            //на 1-це
//            if ($state == 1) {
//                switch ($data->message) {
//                    case 1:
//                        $connection->send(json_encode(['for_client' => "\r\nПриветствуем вас на бескрайних просторах мира чудес и приключений!"]));
//                        $this->characters['user_uuid']['state'] = 2;
//                        break;
////                    case 'stop':
////                        $this->stopDaemon();
////                        break;
//                }
//            } else{
//                $connection->send(json_encode(['for_client' => "\r\nКакой-то левый поц детектед!"]));
//            }

            $character = $this->characters[$dataUserUuid];

//            $stateString = <<<STR
//\r\n
//{$character->HP}H {$character->VP}V 1855881X {$character->coins}C Вых:Ю>
//STR;
            $exits       = 'ю';
            $stateString = "<span style='color:darkgreen'>{$character->HP}H {$character->VP}V 1999X {$character->coins}C Вых:{$exits}></span>";

            switch ($state) {
                //на 1-це
                case 1:
                    switch ($data->message) {
                        case 1:
//                            $connection->send(json_encode(['for_client' => "\r\nПриветствуем вас на бескрайних просторах мира чудес и приключений!" . $stateString]));
//                            $connection->send(json_encode(['for_client' => "<br>Приветствуем вас на бескрайних просторах мира чудес и приключений!" . $stateString]));

                            $helloMessage = "<span>Приветствуем вас на бескрайних просторах мира чудес и приключений!</span>";

                            $this->characters[$dataUserUuid]['state'] = 2;
//                            $this->characters[$dataUserUuid]['room_uuid'] = Room::START_ROOM_UUID;
                            /**/
                            $this->characters[$dataUserUuid]->room_uuid = Room::START_ROOM_UUID;
                            /**/


                            /**/
//                            $debugFile = 'storage\debug1111111-onMessage-111111111111-$this-characters$dataUserUuid.txt';
//                            file_exists($debugFile) ? $current = file_get_contents($debugFile) : $current = null;
//                            $results = print_r( $this->characters[$dataUserUuid], true);
//                            !empty($current) ? $current .= "\r\n" . $results : $current .= "\n" . $results;
//                            file_put_contents($debugFile, $current);
                            /**/

                            $stateString = $this->renderStateString($character, $rooms[Room::START_ROOM_UUID]['exits']);
                            $roomName    = "<span style='color:indigo'>" . $rooms[Room::START_ROOM_UUID]['name'] . "</span>";
                            $connection->send(json_encode(['for_client' => $stateString . $roomName . $helloMessage]));

                            break;
                    }
                    break;
                case 2:
                    switch ($data->message) {
                        case 'см':
//                            $connection->send(json_encode(['for_client' => $stateString . "<span>Лесная поляна на перекрестке тропинок.</span>"]));

//                            $stateString = $this->renderStateString($character, $rooms[Room::START_ROOM_UUID]['exits']);
//                            $roomName    = "<span style='color:indigo'>" . $rooms[Room::START_ROOM_UUID]['name'] . "</span>";
                            $room            = $rooms[$this->characters[$dataUserUuid]['room_uuid']];
                            $stateString     = $this->renderStateString($character, $room['exits']);
                            $roomName        = "<span style='color:indigo'>" . $room['name'] . "</span>";
                            $roomDescription = "<span>" . $room['description'] . "</span>";
                            $connection->send(json_encode(['for_client' => $stateString . $roomDescription . $roomName]));

                            break;
                        case 'сч':
                            $message = <<<STR
Вы {$character->name}, {$character->profession->name} {$character->experience} уровня.<br>
Ваш E-mail: {$character->user->email}<br>
Вы набрали {$character->experience} опыта и имеете {$character->coins} монеты.<br>
STR;

//                            $connection->send(json_encode(['for_client' => '<span style="color:darkgreen">' . "\r\n{$message}{$stateString}" . '</span>']));
//                            $connection->send(json_encode(['for_client' => "<br>{$message}{$stateString}"]));
//                            $connection->send(json_encode(['for_client' => '<text style="color:darkgreen">' . "<br>{$message}{$stateString}" . '</text>']));
//                            $connection->send(json_encode(['for_client' => "<br>{$message}"]));
                            $connection->send(json_encode(['for_client' => '<span>' . "{$message}{$stateString}" . '</span>']));
                            break;
                        case 'east':

                            //Бред
//                            $nextRoomUuid = !empty($this->characters[$dataUserUuid]['room_uuid']['exits']['e']) ? $this->characters[$dataUserUuid]['room_uuid']['exits']['e'] : null;

                            $nextRoomUuid = !empty($rooms[$this->characters[$dataUserUuid]['room_uuid']]['exits']['e']) ? $rooms[$this->characters[$dataUserUuid]['room_uuid']]['exits']['e'] : null;

                            /**/
                            $debugFile = 'storage\debug1111111-onMessage-$this-characters-$dataUserUuid.txt';
                            file_exists($debugFile) ? $current = file_get_contents($debugFile) : $current = null;
                            $results = print_r($this->characters, true);
                            !empty($current) ? $current .= "\r\n" . $results : $current .= "\n" . $results;
                            file_put_contents($debugFile, $current);
                            /**/

                            /**/
                            $debugFile = 'storage\debug1111111-onMessage-$this-$nextRoomUuid.txt';
                            file_exists($debugFile) ? $current = file_get_contents($debugFile) : $current = null;
                            $results = print_r($nextRoomUuid, true);
                            !empty($current) ? $current .= "\r\n" . $results : $current .= "\n" . $results;
                            file_put_contents($debugFile, $current);
                            /**/

                            if ($nextRoomUuid) {
//                                $this->characters[$dataUserUuid]['room_uuid'] = $nextRoomUuid;
                                $this->characters[$dataUserUuid]->room_uuid = $nextRoomUuid;
                                $stateString                                = $this->renderStateString($character, $rooms[$nextRoomUuid]['exits']);
                                $roomName                                   = "<span style='color:indigo'>" . $rooms[$nextRoomUuid]['name'] . "</span>";
                                $connection->send(json_encode(['for_client' => $stateString . $roomName]));
                            } else {
                                $connection->send(json_encode(['for_client' => $stateString . "<span>Вы не можете идти в этом направлении...</span>"]));
                            }


                            break;
                    }
                    break;
            }

            //отправить конкретному пользователю
            $connection->send(json_encode(['current_user' => time()]));

            //рассылка всем
            foreach ($this->connections as $value) {

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
            foreach ($this->connections as $value) {
                $service = json_encode(['service' => 'Пользователь отключился.']);
                $value->send($service);
            }
        };
        Worker::runAll();
    }


    public function renderStateString($character, $exitsArray)
    {
        $north = !empty($exitsArray['n']) ? 'С' : '';
        $east  = !empty($exitsArray['e']) ? 'В' : '';
        $south = !empty($exitsArray['s']) ? 'Ю' : '';
        $west  = !empty($exitsArray['w']) ? 'З' : '';
        $up    = !empty($exitsArray['u']) ? '^' : '';
        $down  = !empty($exitsArray['d']) ? 'v' : '';

        $exits = $north . $east . $south . $west . $up . $down;

        return "<span style='color:darkgreen'>{$character->HP}H {$character->VP}V 1999X {$character->coins}C Вых:{$exits}></span>";
    }
}
