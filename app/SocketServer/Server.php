<?php

namespace App\SocketServer;

//require_once __DIR__ . '../../vendor/autoload.php';
//require_once __DIR__ . '/config.php';

//require_once __DIR__ . '/config.php';
//require_once __DIR__ . '/../vendor/autoload.php';

//use Logger;

use App\Helpers\Debugger;
use App\Helpers\Formulas;
use App\Http\Controllers\CharacterController;
use App\Mobile;
use App\Room;
use App\Services\CharacterService;
use App\Services\MessageService;
use App\Services\UserService;
use App\SocketServer\Contracts\DataInterface;
use App\SocketServer\Contracts\LoggerInterface;
use Faker\Factory;
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

    private $userService;

    private $characterService;

    private $messageService;

    private $roundOfBattle;


    public function __construct(
        UserService $userService,
        CharacterService $characterService,
        MessageService $messageService,
        $config,
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

        $this->roundOfBattle = env("ROUND_OF_BATTLE");
    }


    public function serverStart()
    {
        $rooms      = [];
        $characters = [];

        //грузим зоны
//        $roomsArray = Room::all()->toArray();
        $roomsArray = Room::with('mobiles')->get()->toArray();
        foreach ($roomsArray as $roomArray) {
            $rooms[$roomArray['inner_id']] = $roomArray;
        }

        //грузим мобов
//        $mobiles = Mobile::all()->toArray();
//        foreach ($mobiles as $mobile) {
//            $rooms[$mobile['room_inner_id']]['mobiles'][] = $mobile;
//        }


        /**/
        Debugger::PrintToFile('$rooms', $rooms);
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
        $this->ws_worker->onConnect = function ($connection/*, $data/**/) use (&$users, &$characters) {

            /**/
//            $debugFile = 'storage\debug1111111-onConnect.txt';
//            file_exists($debugFile) ? $current = file_get_contents($debugFile) : $current = null;
//            $results = print_r($users, true);
//            !empty($current) ? $current .= "\r\n" . $results : $current .= "\n" . $results;
//            file_put_contents($debugFile, $current);
            /**/

            $connection->onWebSocketConnect = function ($connection) use (&$users, &$characters) {

                $userEmailFromClient = $_GET['user'];
                $activeCharacter     = $this->characterService->getActiveCharacterByUserEmail($userEmailFromClient);

//                $characters[$activeCharacter->user->uuid] = $activeCharacter;
                $characters[$activeCharacter['user']['uuid']] = $activeCharacter;

                /**/
                Debugger::PrintToFile('--------------++++$character', $activeCharacter);
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
//                $charName = !empty($activeCharacter) ? $activeCharacter->name : "Не выбран";
                $charName = !empty($activeCharacter) ? $activeCharacter['name'] : "Не выбран";

                $selectCharacterDialog = <<<STR
<span>
Аккaунт [{$userEmailFromClient}] Персонаж [{$charName}]<br>
Добро пожаловать в MUD!<br>
0) Выход из MUDа.<br>
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

                $service0 = json_encode(['selectCharacterDialog' => $selectCharacterDialog]);
                /**/
                Debugger::PrintToFile('--------------++++$service0', $service0);
                /**/

                $connection->send($service0);


                /**/
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
        $this->ws_worker->onMessage = function ($connection, $data) use (&$users, $rooms, &$characters) {
            $data = json_decode($data);
            /**/
//            Debugger::PrintToFile('-onMessage-$data', $data);
            /**/

            $time       = date("H:i:s");
            $data->time = $time;
            /**/
//            Debugger::PrintToFile('-onMessage-$this-connections', $this->connections);
            /**/


//            $dataUserUuid = $data->uuid;
            //тут наверное нужно сделать проверку что uuid принадлежит авторизовавшемуся.
            //Как варик передавать токен в каждом запросе и парсить его
            //наверное так и надо сделать
            $userUuidFromClient = $data->uuid;

            // ищем пользователя по уникальному ИД в базе
//            $findUser = $this->userService->findByUuid($data->uuid);

            //начало
            $character = &$characters[$userUuidFromClient];

            $state = !empty($character['state']) ? $character['state'] : 1;

            if ($state == 2) {
//                $stateString = $this->renderStateString($character, $rooms[$character->room_uuid]['exits']);
                $stateString = $this->renderStateString($character, $rooms[$character['room_inner_id']]['exits']);
            }


            switch ($state) {
                //на 1-це
                case 1:
                    switch ($data->message) {
                        case 1:
                            $helloMessage       = "<span style='color:goldenrod'>Приветствуем вас на бескрайних просторах мира чудес и приключений!</span>";
                            $character['state'] = 2;
//                            $character->room_inner_id = Room::START_ROOM_INNER_ID;
                            $character['room_inner_id'] = Room::START_ROOM_INNER_ID;
                            $stateString                = $this->renderStateString($character, $rooms[Room::START_ROOM_INNER_ID]['exits']);
//                            $roomName = "<span style='color:indigo'>" . $rooms[Room::START_ROOM_INNER_ID]['name'] . "</span>";
                            $roomName = "<span class='room-name'>" . $rooms[Room::START_ROOM_INNER_ID]['name'] . "</span>";
                            $connection->send(json_encode(['for_client' => $stateString . $roomName . $helloMessage]));

                            break;
                    }
                    break;
                case 2:
                    switch ($data->message) {
                        case 'сч':
                        case 'сче':
                        case 'счет':
                            $message = <<<STR
Вы <span style='color:goldenrod'>{$character['name']}</span>, {$character['profession']['name']} {$character['experience']} уровня.<br>
Ваш E-mail: {$character['user']['email']}<br>
Вы набрали {$character['experience']} опыта и имеете {$character['coins']} монеты.<br>
STR;
                            $connection->send(json_encode(['for_client' => '<span>' . "{$message}{$stateString}" . '</span>']));
                            break;

                        case 'см':
                            $connection->send(json_encode(['for_client' => $this->renderRequestOnLook($character, $rooms)]));
                            break;

                        case 'north':
                            $connection->send(json_encode(['for_client' => $this->renderRequestOnMove($character, $rooms, $stateString, 'n')]));
                            break;

                        case 'east':
                            $connection->send(json_encode(['for_client' => $this->renderRequestOnMove($character, $rooms, $stateString, 'e')]));
                            break;

                        case 'south':
                            $connection->send(json_encode(['for_client' => $this->renderRequestOnMove($character, $rooms, $stateString, 's')]));
                            break;

                        case 'west':
                            $connection->send(json_encode(['for_client' => $this->renderRequestOnMove($character, $rooms, $stateString, 'w')]));
                            break;

                        case (preg_match('/^осм.*/', $data->message) ? true : false) :
                        case (preg_match('/^осмо.*/', $data->message) ? true : false) :
                        case (preg_match('/^осмот.*/', $data->message) ? true : false) :
                        case (preg_match('/^осмотр.*/', $data->message) ? true : false) :
                        case (preg_match('/^осмотре.*/', $data->message) ? true : false) :
                        case (preg_match('/^осмотрет.*/', $data->message) ? true : false) :
                        case (preg_match('/^осмотреть.*/', $data->message) ? true : false) :
//                        case (stripos($data->message, 'осм') ? true : false) :
//                        case preg_match('#^/oop/page/осм/\d+$#', $data->message):


                            $dataMessage = $data->message;
                            $argument    = mb_strtolower(trim(substr($dataMessage, strpos($dataMessage, ' '))));
                            $room        = $rooms[$character['room_inner_id']];
                            $description = '';

                            if (!empty($room['mobiles'])) {
                                foreach ($room['mobiles'] as $mobile) {
                                    foreach ($mobile['pseudonyms'] as $pseudonym) {

                                        /**/
//                                        Debugger::PrintToFile('--$room---------осм+++++++++', $room);
//                                        Debugger::PrintToFile('--$room-555555555555555555', mb_strtolower(trim(mb_substr($pseudonym, 0, mb_strlen($argument)))));
//                                        Debugger::PrintToFile('--$room-555555555555555556', $argument);
                                        /**/

                                        if (mb_strtolower(trim(mb_substr($pseudonym, 0, mb_strlen($argument)))) == $argument) {

                                            $description = "<span>" . $mobile['description'] . "</span>";
                                            break;
                                        }
                                    }
                                }
                            }


                            $connection->send(json_encode(['for_client' => $stateString . $description]));
                            break;


                        case 'ум':
                            $tableRows = '';
                            /**/
                            Debugger::PrintToFile('ум', $character);
                            /**/

//                            if (!empty($character['skills'])) {
//                                foreach ($character['skills'] as $skill) {
                            if (!empty($character['profession']['skills'])) {
                                foreach ($character['profession']['skills'] as $skill) {

                                    /**/
//                                    Debugger::PrintToFile('learning_level_check', $skill->learning_level_check->first()->learning_level);
                                    /**/

//                                    if ($character['level'] >= (int)$skill['learning_level_check'][0]['learning_level']) {
//                                        $tableRows .= <<<STR
//<tr>
//  <th></th>
//  <td width="30%">{$skill['name']}</td>
//  <td>{$skill['pivot']['value']}</td>
//  <td></td>
//</tr>
//STR;
//                                    }

                                    if ($character['level'] >= $skill['pivot']['learning_level']) {
                                        $tableRows .= <<<STR
<tr>
  <th></th>
  <td width="30%">{$skill['name']}</td>
  <td>{$skill['pivot']['value']}</td>
  <td></td>
</tr>
STR;
                                    }

                                }
                            }

                            $skillsTable = <<<STR
<table>
  <thead>
    <tr>
      <th></th>
      <th width="30%"></th>
      <th></th>
      <th></th>
    </tr>
  </thead>
  <tbody>
  {$tableRows}
  </tbody>
</table>
Ваши умения:
STR;

                            $connection->send(json_encode(['for_client' => $stateString . $skillsTable]));
                            break;

//                        case 'у':
//                        case 'уд':
                        case (preg_match('/^у.*/', $data->message) ? true : false) :
                        case (preg_match('/^уд.*/', $data->message) ? true : false) :

                            $dataMessage = $data->message;
                            $argument    = mb_strtolower(trim(substr($dataMessage, strpos($dataMessage, ' '))));
                            $room        = $rooms[$character['room_inner_id']];

                            if (!empty($room['mobiles'])) {
                                foreach ($room['mobiles'] as $mobile) {
                                    foreach ($mobile['pseudonyms'] as $pseudonym) {
                                        if (mb_strtolower(trim(mb_substr($pseudonym, 0, mb_strlen($argument)))) == $argument) {
                                            $character['opponent'] = $mobile;
                                            //режим "в бою"
                                            $character['state'] = 3;
                                            break;
                                        }
                                    }
                                }
                            }

                            /**/
                            Debugger::PrintToFile('--Бой-$character', $character);
                            /**/

                            $faker = Factory::create();

                            $damage        = $faker->numberBetween($character['first_damage_min'], $character['first_damage_max']);
                            $damageMessage = Formulas::damageMessage($damage);
                            $opponentName  = $character['opponent']['name'];
                            $actorMessage  = "<span  style='color:#CA5209'>Вы $damageMessage рубанули $opponentName. ($damage)</span>";
                            $connection->send(json_encode(['for_client' => $stateString . $actorMessage]));

                            $timerId = Timer::add($this->roundOfBattle, function () use ($connection, $stateString, $character, $faker) {
                                $damage          = $faker->numberBetween($character['first_damage_min'], $character['first_damage_max']);
                                $damageMessage   = Formulas::damageMessage($damage);
                                $opponentName    = $character['opponent']['name'];
                                $actorMessage    = "<span  style='color:#23CD18'>Вы $damageMessage рубанули $opponentName. ($damage)</span>";
                                $opponentMessage = "<span  style='color:#CA5209'>$opponentName попытался огреть вас, но не смог этого сделать</span>";
                                $connection->send(json_encode(['for_client' => $stateString . $opponentMessage . $actorMessage]));
                            });

                            $character['timer_id'] = $timerId;
                            break;

                        case 'стоп':
                            Timer::del($character['timer_id']);
                            $connection->send(json_encode(['for_client' => $stateString . "Вы решили остановить кровопролитие..."]));
                            break;
                        case 'э':
                        case 'эк':
                        case 'эки':
                        case 'экип':
                        case 'экипи':
                        case 'экипир':
                        case 'экипиро':
                        case 'экипиров':
                        case 'экипировк':
                        case 'экипировка':
                            $tableRows = '';
                            foreach ($character['stuff'] as $item) {
                                //если слот вещи соответствует слоту чара
                                if ($item['slot_id'] == $item['pivot']['slot_id']) {

                                    $itemName  = mb_strtolower($item['name']);
                                    $tableRows .= <<<STR
<tr>
  <td width="30%">{$item['slot']['name']}</td>
  <td>{$itemName}</td>
  <td></td>
</tr>
STR;
                                }
                            }

                            $equipmentTable = <<<STR
<table>
  <thead>
    <tr>
      <th width="30%"></th>
      <th></th>
      <th></th>
    </tr>
  </thead>
  <tbody>
  {$tableRows}
  </tbody>
</table>
Вы используете:
STR;
                            $connection->send(json_encode(['for_client' => $stateString . $equipmentTable]));
                            break;
                    }

                    break;
            }

            //отправить конкретному пользователю
//            $connection->send(json_encode(['current_user' => time()]));

            //рассылка всем
            foreach ($this->connections as $value) {
                /**/
//                Debugger::PrintToFile('--$data', $data);
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

        return "<span style='color:darkgreen'>{$character['HP']}H {$character['VP']}V {$character['to_next_level']}X {$character['coins']}C Вых:{$exits}></span>";
    }


    public function renderRequestOnLook($character, $rooms)
    {
        $room            = $rooms[$character['room_inner_id']];
        $stateString     = $this->renderStateString($character, $room['exits']);
        $roomName        = "<span style='color:indigo'>" . $room['name'] . "</span>";
        $roomDescription = "<span>" . $room['description'] . "</span>";

        $mobileTitle = '';
        if (!empty($room['mobiles'])) {
            foreach ($room['mobiles'] as $mobiles) {
                $mobileTitle .= "<span style='color:#CA5209'>" . $mobiles['title_inside_of_room'] . "</span>";
            }
        }

        return $stateString . $mobileTitle . $roomDescription . $roomName;
    }

    public function renderRequestOnMove(&$character, $rooms, $stateString, $direction)
    {
        $nextRoomInnerId = !empty($rooms[$character['room_inner_id']]['exits'][$direction]) ? $rooms[$character['room_inner_id']]['exits'][$direction] : null;
        if ($nextRoomInnerId) {

            $character['room_inner_id'] = $nextRoomInnerId;
            $room                       = $rooms[$character['room_inner_id']];
            $stateString                = $this->renderStateString($character, $rooms[$nextRoomInnerId]['exits']);
            $roomName                   = "<span style='color:indigo'>" . $rooms[$nextRoomInnerId]['name'] . "</span>";
            $mobileTitle                = '';

            if (!empty($room['mobiles'])) {
                foreach ($room['mobiles'] as $mobiles) {
                    $mobileTitle .= "<span style='color:#CA5209'>" . $mobiles['title_inside_of_room'] . "</span>";
                }
            }

            return $stateString . $mobileTitle . $roomName;
        } else {
            return $stateString . "<span>Вы не можете идти в этом направлении...</span>";
        }
    }
}
