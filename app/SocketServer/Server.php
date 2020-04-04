<?php

namespace App\SocketServer;

use App\Helpers\Constants;
use App\Helpers\Debugger;
use App\Helpers\Formulas;
use App\Http\Controllers\CharacterController;
use App\Jobs\SaveCharacterJob;
use App\Mobile;
use App\Profession;
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

    /**/
    private $strToLower;

    /**/


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

        $this->logger = $logger;
        $this->ws_worker->count = $config['countWorkers'];
        $this->config = $config;

        $this->userService = $userService;
        $this->characterService = $characterService;
        $this->messageService = $messageService;

        $this->roundOfBattle = env("ROUND_OF_BATTLE");
        /**/
        $this->strToLower = function () {
            return 'value';
        };
//        $this->strToLower = 'mb_strtolower';
        /**/
    }

    public function __call($name, $arguments)
    {
        if ($name == 'strToLower') {
            return mb_strtolower($arguments[0]);
        }
    }

    public function serverStart()
    {
        $rooms = [];
        $characters = [];

        //грузим зоны
//        $roomsArray = Room::all()->toArray();
        $roomsArray = Room::with('mobiles')->get()->toArray();

        /**/
//        Debugger::PrintToFile('$roomsArray', $roomsArray);
        /**/

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

        $this->connections = [];
        /*
        * Стартуем сервер и пингуем БД каждую минуту, чтобы сохранить подключение
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

            $connection->onWebSocketConnect = function ($connection) use (&$users, &$characters) {

                $userEmailFromClient = $_GET['user'];
                $activeCharacter = $this->characterService->getActiveCharacterByUserEmail($userEmailFromClient);

                $characters[$activeCharacter['user']['uuid']] = $activeCharacter;

                /**/
                Debugger::PrintToFile('--------------++++$character', $activeCharacter);

                Debugger::PrintToFile('--------------++++onConnect-$users', $users);
                /**/


                //это сообщение будет отправлено клиенту
                /*---на 1-це---*/
                $charName = !empty($activeCharacter) ? $activeCharacter['name'] : "Не выбран";
                $selectCharacterDialog = $this->renderSelectCharacterDialog($userEmailFromClient, $charName);

//                $connection->send(json_encode(['selectCharacterDialog' => $selectCharacterDialog]));
                $connection->send(json_encode(['for_client' => $selectCharacterDialog]));


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
        $this->ws_worker->onMessage = function ($connection, $data) use (&$users, &$rooms, &$characters) {
            $data = json_decode($data);
            /**/
            Debugger::PrintToFile('-onMessage-$data', $data);
            /**/

            $time = date("H:i:s");
            $data->time = $time;
            /**/
//            Debugger::PrintToFile('-onMessage-$this-connections', $this->connections);
            /**/


            //тут наверное нужно сделать проверку что uuid принадлежит авторизовавшемуся.
            //Как варик передавать токен в каждом запросе и парсить его
            //наверное так и надо сделать
            $userUuidFromClient = $data->uuid;

            // ищем пользователя по уникальному ИД в базе
//            $findUser = $this->userService->findByUuid($data->uuid);

            //начало
            $character = &$characters[$userUuidFromClient];

            if (in_array($character['state'], [Constants::STATE_IN_GAME, Constants::STATE_IN_BATTLE])) {
                $stateString = $this->renderStateString($character, $rooms[$character['room_inner_id']]['exits']);
            }

            /**/
            Debugger::PrintToFile('-+++++++onMessage++$character', $character);
            /**/

            switch (true) {
                /*---зашел в игру---*/
                case $character['state'] == Constants::STATE_MENU && $data->message == 1:
                    $helloMessage = "<br><span class='contrast-color'>Приветствуем вас на бескрайних просторах мира чудес и приключений!</span><br>";
                    $character['state'] = Constants::STATE_IN_GAME;
                    $character['room_inner_id'] = Room::START_ROOM_INNER_ID;
                    $stateString = $this->renderStateString($character, $rooms[Room::START_ROOM_INNER_ID]['exits']);
                    $roomName = "<span class='room-name'>" . $rooms[Room::START_ROOM_INNER_ID]['name'] . "</span><br>";
                    $connection->send(json_encode(['for_client' => $helloMessage . $roomName . $stateString]));

                    /**/
                    //тут мб таймер на восстановление хитов
                    if (empty($character['regeneration_HP_timer'])) {

                        /**/
                        Debugger::PrintToFile('---на зашел в игру---000' . time(), $character);
                        /**/

                        //не удаляется сразу, когда на 7 жмакаешь. 1 раз отрабатывает скот
                        $timerId = Timer::add($this->roundOfBattle, function () use ($connection, $rooms, &$character) {
                            /**/
//                        Debugger::PrintToFile('---на зашел в игру---', $character['HP']);
//                        Debugger::PrintToFile('---на зашел в игру---maxHP', $character['maxHP']);
//                            Debugger::PrintToFile('---на зашел в игру---' . time(), $character);
                            /**/
                            if (in_array($character['state'], [Constants::STATE_IN_GAME, Constants::STATE_IN_BATTLE])) {
                                if ($character['HP'] < $character['maxHP']) {
                                    $character['HP']++;
                                }
                            }

                        });
                        $character['regeneration_HP_timer'] = $timerId;
                    }

                    /**/
                    break;

                /*---в игре, но не в бою---*/
                //???

                /*---и в игре, и в бою---*/
                case in_array($character['state'], [
                        Constants::STATE_IN_GAME,
                        Constants::STATE_IN_BATTLE
                    ]) && preg_match("/^north$/", $data->message):
                    $connection->send(json_encode(['for_client' => $this->renderRequestOnMove($character, $rooms, $stateString, 'n')]));

                    /**/

                    /**/
                    Debugger::PrintToFile('-----------$this-connections ', $this->connections);
                    /**/

                    foreach ($this->connections as $value) {
                        $service = json_encode(['service' => "Пользователь  присоединился."]);
                        $value->send($service);
                    }
                    /**/

                    break;
                case  in_array($character['state'], [
                        Constants::STATE_IN_GAME,
                        Constants::STATE_IN_BATTLE
                    ]) && preg_match("/^east$/", $data->message):
                    $connection->send(json_encode(['for_client' => $this->renderRequestOnMove($character, $rooms, $stateString, 'e')]));
                    break;
                case  in_array($character['state'], [
                        Constants::STATE_IN_GAME,
                        Constants::STATE_IN_BATTLE
                    ]) && preg_match("/^south$/", $data->message):
                    $connection->send(json_encode(['for_client' => $this->renderRequestOnMove($character, $rooms, $stateString, 's')]));
                    break;
                case  in_array($character['state'], [
                        Constants::STATE_IN_GAME,
                        Constants::STATE_IN_BATTLE
                    ]) && preg_match("/^west$/", $data->message):
                    $connection->send(json_encode(['for_client' => $this->renderRequestOnMove($character, $rooms, $stateString, 'w')]));
                    break;

                case  in_array($character['state'], [
                        Constants::STATE_IN_GAME,
                        Constants::STATE_IN_BATTLE
                    ]) && preg_match("/^up$/", $data->message):
                    $connection->send(json_encode(['for_client' => $this->renderRequestOnMove($character, $rooms, $stateString, 'u')]));
                    break;

                case  in_array($character['state'], [
                        Constants::STATE_IN_GAME,
                        Constants::STATE_IN_BATTLE
                    ]) && preg_match("/^down$/", $data->message):
                    $connection->send(json_encode(['for_client' => $this->renderRequestOnMove($character, $rooms, $stateString, 'd')]));
                    break;

                //счет
                case in_array($character['state'], [
                        Constants::STATE_IN_GAME,
                        Constants::STATE_IN_BATTLE
                    ]) && preg_match("/^сч(е)?(т)?$/", $data->message):

                    $currentHP = $character['HP'];
                    $maxHP = Formulas::getMaxHP($character);

                    /**/
                    $conditionEstimateArray = Formulas::getConditionEstimate($character['HP'], $character['maxHP']);
                    //todo нормальный schemeId
                    $conditionClass = Constants::getConditionEstimateCssClass(1, $conditionEstimateArray['color_level']);
                    /**/
                    $message = <<<STR
<span class='basic-color'>Вы </span><span style='color:goldenrod'>{$character['name']}</span><span class='basic-color'>, {$character['profession']['name']} {$character['level']} уровня.</span><br>
<span class='basic-color'>Ваш E-mail: {$character['user']['email']}</span><br>
<span class='basic-color'>Слава: {$character['glory']}</span><br>
<span class='basic-color'>Вы имеете <span class='{$conditionClass}'>{$currentHP}</span>(<span class='health-good'>{$maxHP}</span>) единиц здоровья.</span><br>
<span class='basic-color'>Вы набрали {$character['experience']} опыта и имеете </span><span style='color:gold'>{$character['coins']}</span><span class='basic-color'> монет.</span><br>
<span class='basic-color'>У вас есть {$character['trainings_count']} тренировок.</span><br>
STR;
                    $connection->send(json_encode(['for_client' => '<span>' . "{$message}{$stateString}" . '</span>']));
                    break;

                //смотреть
                case in_array($character['state'], [
                        Constants::STATE_IN_GAME,
                        Constants::STATE_IN_BATTLE
                    ]) && preg_match("/^см(о)?(т)?$/", $data->message):
                    $connection->send(json_encode(['for_client' => $this->renderRequestOnLook($character, $rooms)]));
                    break;

                //осмотреть
                //todo не работает
                case in_array($character['state'], [
                        Constants::STATE_IN_GAME,
                        Constants::STATE_IN_BATTLE
                    ]) && preg_match("/^осм(о)?(т)?(р)?(е)?(т)?(ь)?.*/", $data->message):
                    $dataMessage = $data->message;
                    $argument = mb_strtolower(trim(mb_substr($dataMessage, strpos($dataMessage, ' '))));
                    $room = $rooms[$character['room_inner_id']];
                    $description = '';
                    if (!empty($room['mobiles'])) {
                        foreach ($room['mobiles'] as $mobile) {
                            if (!empty($mobile)) {
                                foreach ($mobile['pseudonyms'] as $pseudonym) {
                                    if (mb_strtolower(trim(mb_substr($pseudonym, 0, mb_strlen($argument)))) == $argument) {
                                        $description = "<span class='basic-color'>" . $mobile['description'] . "</span>";
                                        break;
                                    }
                                }
                            }
                        }
                    }

                    $connection->send(json_encode(['for_client' => $stateString . $description]));
                    break;

                //умения
                case in_array($character['state'], [
                        Constants::STATE_IN_GAME,
                        Constants::STATE_IN_BATTLE
                    ]) && preg_match("/^ум(е)?(н)?(и)?(я)?$/", $data->message):
                    $tableRows = '';
                    if (!empty($character['skills'])) {
                        foreach ($character['skills'] as $skill) {
                            if ($character['level'] >= $skill['learning_level']) {
                                $tableRows .= <<<STR
<tr>
  <th></th>
  <td width="30%">{$skill['name']}</td>
  <td>{$skill['value']}</td>
  <td></td>
</tr>
STR;
                            }
                        }
                    }

                    $skillsTable = <<<STR
<span style='color:gold'>Ваши умения:</span>
<table class="equipment">
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
STR;
                    $connection->send(json_encode(['for_client' => $skillsTable . $stateString]));
                    break;

                //todo это очень криво
                //экипировка
                case in_array($character['state'], [
                        Constants::STATE_IN_GAME,
                        Constants::STATE_IN_BATTLE
                    ]) && preg_match("/^э(к)?(и)?(п)?(и)?(р)?(о)?(в)?(к)?(а)?$/", $data->message):

                    $tableRows = '';
                    foreach ($character['stuff'] as $item) {
                        //если слот вещи соответствует слоту чара
                        if ($item['slot_id'] == $item['pivot']['slot_id']) {
                            $itemName = mb_strtolower($item['name']);
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
<span class='contrast-color'>Вы используете:</span>
<table class="equipment">
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
STR;
                    $connection->send(json_encode(['for_client' => $equipmentTable . $stateString]));
                    break;

                //инвентарь
                case in_array($character['state'], [
                        Constants::STATE_IN_GAME,
                        Constants::STATE_IN_BATTLE
                    ]) && preg_match("/^и(н)?(в)?(е)?(н)?(т)?(а)?(р)?(ь)?$/", $data->message):

                    $inventoryItems = $this->characterService->getInventoryItems($character);

                    $inventoryItemsStr = '';
                    foreach ($inventoryItems as $inventoryItem) {
                        $inventoryItemsStr .= "<span class='basic-color'>{$this->strToLower($inventoryItem['name'])}</span><br>";
                    }
                    $message = <<<STR
<span>
    <span class='basic-color'>Вы несете:</span><br>
    {$inventoryItemsStr}
</span>
STR;
//                    $connection->send(json_encode(['for_client' => $this->renderStateString($character, $rooms[$character['room_inner_id']]['exits']) . $message]));
                    $connection->send(json_encode(['for_client' => $message . $this->renderStateString($character, $rooms[$character['room_inner_id']]['exits'])]));
                    break;

                /**/
                //трен
                case ($character['state'] == Constants::STATE_IN_GAME) && preg_match("/^(тр|тре|трен|трени|тренир|трениро|трениров|тренирова|тренироват|тренировать)\s.*/", $data->message):


                    if (!$this->isItPossibleToTrain($character, $rooms)) {
                        $message = "<span class='basic-color'>Вы можете сделать это только в своей гильдии или у странствующего учителя</span><br>";
                        $connection->send(json_encode(['for_client' => $message . $stateString]));
                    } else {
                        $dataMessage = $data->message;
                        $argument = mb_strtolower(trim(substr($dataMessage, strpos($dataMessage, ' '))));

                        $message = "<span class='basic-color'>Вы потренировали силу за 1 тренировку.</span><br>";
                        $connection->send(json_encode(['for_client' => $message . $stateString]));
                    }

//                    $connection->send(json_encode(['for_client' => $this->renderRequestOnMove($character, $rooms, $stateString, 'n')]));
                    break;

                /**/


                //ударить
                case in_array($character['state'], [
                        Constants::STATE_IN_GAME,
                        Constants::STATE_IN_BATTLE
                    ])
//                    && preg_match("/^у(д)?(а)?(р)?(и)?(т)?(ь)?.*/", $data->message)
                    && preg_match("/^(у|уд|уда|удар|удари|ударит|ударить)\s.*/", $data->message):


                    /**/
                    if (!empty($character['opponent'])) {
                        $opponentMessage = "<span class='basic-color'>Невозможно! вы уже сражаетесь с {$character['opponent']['name']}.</span><br>";
                        $connection->send(json_encode(['for_client' => $opponentMessage . $stateString]));
                        break;
                    }
                    /**/

                    $dataMessage = $data->message;
                    $argument = mb_strtolower(trim(substr($dataMessage, strpos($dataMessage, ' '))));
                    $room = $rooms[$character['room_inner_id']];

                    /**/
                    Debugger::PrintToFile('--Бой-$argument', $argument);
                    /**/

                    /**/
                    Debugger::PrintToFile('--Бой-$room', $room);
                    /**/

                    if (!empty($room['mobiles'])) {
                        for ($i = 0; $i <= count($room['mobiles']); $i++) {
                            if (!empty($room['mobiles'][$i])) {
                                foreach ($room['mobiles'][$i]['pseudonyms'] as $pseudonym) {
                                    if (mb_strtolower(trim(mb_substr($pseudonym, 0, mb_strlen($argument)))) == $argument) {
                                        $character['opponent'] = &$rooms[$character['room_inner_id']]['mobiles'][$i];
                                        break 2;
                                    }
                                }
                            }
                        }
                    }

                    /**/
                    Debugger::PrintToFile('--Бой-$character', $character);
                    /**/

                    if (empty($character['opponent'])) {
                        $opponentMessage = "<span class='basic-color'>Похоже, здесь нет этого.</span><br>";
//                        $connection->send(json_encode(['for_client' => $stateString . $opponentMessage]));
                        $connection->send(json_encode(['for_client' => $opponentMessage . $stateString]));
                        break;
                    }
                    /**/
                    Debugger::PrintToFile('--Бой-break', 'break');
                    /**/
                    //ставим режим "в бою"
                    $character['state'] = Constants::STATE_IN_BATTLE;
                    $faker = Factory::create();

                    $damage = $faker->numberBetween($character['first_damage_min'], $character['first_damage_max']);
                    if ($damage < $character['opponent']['HP']) {
                        $damageMessage = Formulas::damageMessage($damage);
                        $actorMessage = "<span class='actor-attack'>Вы $damageMessage рубанули {$character['opponent']['name']}. ($damage)</span><br>";
                        $character['opponent']['HP'] -= $damage;
//                        $connection->send(json_encode(['for_client' => $this->renderStateString($character, $rooms[$character['room_inner_id']]['exits']) . $actorMessage]));
                        $connection->send(json_encode(['for_client' => $actorMessage . $this->renderStateString($character, $rooms[$character['room_inner_id']]['exits'])]));

                        $timerId = Timer::add($this->roundOfBattle, function () use ($connection, $rooms, &$character, $faker) {
                            $actorDamage = $faker->numberBetween($character['first_damage_min'], $character['first_damage_max']);

                            if ($actorDamage < $character['opponent']['HP']) {
                                $damageMessage = Formulas::damageMessage($actorDamage);
                                $actorMessage = "<span class='actor-attack'>Вы $damageMessage рубанули {$character['opponent']['name']}. ($actorDamage)</span><br>";
                                $character['opponent']['HP'] -= $actorDamage;
                                $opponentMessage = '';
                                $opponentDamage = 0;
                                for ($i = 1; $i <= $character['opponent']['attacks_number']; $i++) {
                                    ${"opponentDamage{$i}"} = $faker->numberBetween($character['opponent']['damage_min'], $character['opponent']['damage_max']);
                                    ${"opponentMessage{$i}"} = Formulas::damageMessage(${"opponentDamage{$i}"});
                                    /**/
//                                    Debugger::PrintToFile('--Бой-$opponentMessagei', ${"opponentMessage{$i}"});
                                    /**/
                                    $opponentDamage += ${"opponentDamage{$i}"};
                                    $opponentMessage .= "<span class='enemy-attack'>{$character['opponent']['name']} ${"opponentMessage{$i}"} ударил вас!</span><br>";
                                }

                                $character['HP'] -= $opponentDamage;
                                /**/
                                Debugger::PrintToFile('--Бой-$opponentMessage', $opponentMessage);
                                /**/

//                                $connection->send(json_encode(['for_client' => $this->renderStateString($character, $rooms[$character['room_inner_id']]['exits']) . $opponentMessage . $actorMessage]));
                                $connection->send(json_encode(['for_client' => $actorMessage . $opponentMessage . $this->renderStateString($character, $rooms[$character['room_inner_id']]['exits'])]));
                            } else {
                                Timer::del($character['fight_timer']);
                                $character['fight_timer'] = null;
                                $character['state'] = Constants::STATE_IN_GAME;

                                $addingExperience = $this->characterService->addingExperience($character, $character['opponent']['exp_reward']);
                                if (!empty($addingExperience['got_new_level'])) {
                                    $newLevelMessage = "<span class='contrast-color'>Вы поднялись на уровень!</span><br>";
                                    $this->characterService->setLevelUpCharacteristic($character);
                                } else {
                                    $newLevelMessage = "";
                                }

                                $actorMessage = <<<STR
<span>                                
    <span class='actor-attack'>Вы аккуратно разрезали {$this->strToLower($character['opponent']['name'])} на две части ($actorDamage)</span><br>
    <span class='basic-color'>{$character['opponent']['name']} мертв! R.I.P.</span><br>
    <span class='basic-color'>Вы получили {$addingExperience['experienceReward']} единиц опыта.</span><br>
    {$newLevelMessage}
</span>
STR;
                                /**/
                                //удалить моба
                                $character['opponent'] = null;

//                                $connection->send(json_encode(['for_client' => $this->renderStateString($character, $rooms[$character['room_inner_id']]['exits']) . $actorMessage]));
                                $connection->send(json_encode(['for_client' => $actorMessage . $this->renderStateString($character, $rooms[$character['room_inner_id']]['exits'])]));
                                /**/
                                Debugger::PrintToFile('--Бой-SaveCharacterJob', $character);
                                /**/
                                dispatch(new SaveCharacterJob($character));
                            }

                        });

                        $character['fight_timer'] = $timerId;
                    } else {
                        $character['state'] = Constants::STATE_IN_GAME;
                        $addingExperience = $this->characterService->addingExperience($character, $character['opponent']['exp_reward']);
                        if (!empty($addingExperience['got_new_level'])) {
                            $newLevelMessage = "<span class='contrast-color'>Вы поднялись на уровень!</span><br>";
//                            $character['maxHP'] = Formulas::getMaxHP($character);
                            $this->characterService->setLevelUpCharacteristic($character);

                        } else {
                            $newLevelMessage = "";
                        }
                        $actorMessage = <<<STR
<span>
    <span class='actor-attack'>Одним ударом вы отправили {$this->strToLower($character['opponent']['name'])} в мир иной!!! ($damage)</span><br>
    <span class='basic-color'>{$character['opponent']['name']} мертв! R.I.P.</span><br>
    <span class='basic-color'>Вы получили {$addingExperience['experienceReward']} единиц опыта.</span><br>
    {$newLevelMessage}
</span>
STR;
                        $character['opponent'] = null;
                        if (!empty($character['fight_timer'])) {
                            Timer::del($character['fight_timer']);
                            $character['fight_timer'] = null;
                        }
//                        $connection->send(json_encode(['for_client' => $this->renderStateString($character, $rooms[$character['room_inner_id']]['exits']) . $actorMessage]));
                        $connection->send(json_encode(['for_client' => $actorMessage . $this->renderStateString($character, $rooms[$character['room_inner_id']]['exits'])]));

                        /**/
                        Debugger::PrintToFile('--Бой-SaveCharacterJob-1удар', $character);
                        /**/

                        dispatch(new SaveCharacterJob($character));
                    }


                    /**/
                    //???
//                    unset($character['opponent']);
                    /**/
                    break;

                //просто ENTER
                case in_array($character['state'], [
                        Constants::STATE_IN_GAME,
                        Constants::STATE_IN_BATTLE
                    ]) && $data->message == 'empty_string':
                    $connection->send(json_encode(['for_client' => $stateString]));

                    break;

                /*---чисто в бою---*/
                case  $character['state'] == Constants::STATE_IN_BATTLE && $data->message == 'стоп':

                    Timer::del($character['fight_timer']);
                    $character['state'] = Constants::STATE_IN_GAME;
                    unset($character['opponent']);

                    $connection->send(json_encode(['for_client' => $this->renderStateString($character, $rooms[$character['room_inner_id']]['exits']) . "<span class='contrast-color'>Вы решили остановить кровопролитие...</span>"]));

                    break;


                /**/
                //todo сделать нормальное сохранение по таймеру
                case preg_match("/^сохр$/", $data->message):
                    dispatch(new SaveCharacterJob($character));
                    $connection->send(json_encode(['for_client' => $this->renderStateString($character, $rooms[$character['room_inner_id']]['exits'])]));
                    break;
                //todo перенести в режим бога
                case preg_match("/^хил$/", $data->message):
                    $character['HP'] = $character['maxHP'];
                    dispatch(new SaveCharacterJob($character));
                    $connection->send(json_encode(['for_client' => $this->renderStateString($character, $rooms[$character['room_inner_id']]['exits'])]));
                    break;
                /**/

                //создание нового персонажа
                case $character['state'] == Constants::STATE_MENU && $data->message == Constants::USER_INPUT_CREATE_NEW_CHARACTER:
                    //если есть таймеры текущего персонажа - удаляем их
                    if (!empty($character['fight_timer'])) {
                        Timer::del($character['fight_timer']);
                    }
                    if (!empty($character['regeneration_HP_timer'])) {
                        Timer::del($character['regeneration_HP_timer']);
                    }

                    /**/
//                    Debugger::PrintToFile('--USER_INPUT_CREATE_NEW_CHARACTER-', $characters);
                    /**/
//                    unset($character);
//                    unset($characters[$character['user']['uuid']]);
                    $characters[$character['user']['uuid']] = null;
//                    $character = [];
//                    unset($character);
                    /**/
//                    Debugger::PrintToFile('--USER_INPUT_CREATE_NEW_CHARACTER-2', $characters);
                    /**/
                    /**/
//                    Debugger::PrintToFile('--USER_INPUT_CREATE_NEW_CHARACTER-$character', $character);
                    /**/

                    $message = "<span class='basic-color'>Введите имя персонажа:</span><br>";
                    $connection->send(json_encode(['for_client' => $message]));
                    $character['state'] = Constants::ENTER_NEW_CHARACTER_NAME;
                    break;

                case $character['state'] == Constants::ENTER_NEW_CHARACTER_NAME:
                    //todo нормальная функция подстроки до пробела или до конца
                    $character['name'] = !empty(strpos($data->message, ' ')) ? trim(mb_substr($data->message, 0, strpos($data->message, ' '))) : trim($data->message);
                    $message = <<<STR
<span class='basic-color'>Выберите профессию вашего персонажа.</span>
<br>
<table class="equipment">
    <thead>
    <tr>
        <th width="1%"></th>
        <th width="10%"></th>
        <th></th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td width="1%">1)</td>
        <td width="10%">Воин</td>
        <td>Специалист в области боя.</td>
        <td></td>
    </tr>
        <tr>
        <td width="1%">2)</td>
        <td width="10%">Маг</td>
        <td>Обладает мощными разрушительными заклинаниями.(В разработке...)</td>
        <td></td>
    </tr>
        <tr>
        <td width="1%">3)</td>
        <td width="10%">Лекарь</td>
        <td>Обладает мощной защитной и исцеляющей магией.(В разработке...)</td>
        <td></td>
    </tr>
        <tr>
        <td width="1%">4)</td>
        <td width="10%">Вор</td>
        <td>Специалист по присвоению чужого имущества.(В разработке...)</td>
        <td></td>
    </tr>
    </tbody>
</table>
<span class='basic-color'>Профессия персонажа:</span>
STR;
                    /**/
                    Debugger::PrintToFile('--CREATE_NEW_CHARACTER-name', $character['name']);
                    /**/
                    $connection->send(json_encode(['for_client' => $message]));
                    $character['state'] = Constants::ENTER_NEW_CHARACTER_PROFESSION;
                    break;

                case $character['state'] == Constants::ENTER_NEW_CHARACTER_PROFESSION && in_array($data->message, [Constants::USER_INPUT_CREATE_PROFESSION_WARRIOR]):

                    $character['profession_id'] = 0;
                    switch (true) {
                        case $data->message == Constants::USER_INPUT_CREATE_PROFESSION_WARRIOR:
                            $character['profession_id'] = Profession::WARRIOR_ID;
                            break;
                    }

                    /**/
                    Debugger::PrintToFile('--ENTER_NEW_CHARACTER_PROFESSION-', $character);
                    /**/

                    //todo сделать нормально
                    $character['user_id'] = 1;

                    $character = $this->characterService->createCharacter($character);

                    //todo сделать нормально
                    $userEmailFromClient = 'ыыы';
                    $selectCharacterDialog = $this->renderSelectCharacterDialog($userEmailFromClient, $character['name']);

                    $connection->send(json_encode(['for_client' => $selectCharacterDialog]));

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
        $east = !empty($exitsArray['e']) ? 'В' : '';
        $south = !empty($exitsArray['s']) ? 'Ю' : '';
        $west = !empty($exitsArray['w']) ? 'З' : '';
        $up = !empty($exitsArray['u']) ? '^' : '';
        $down = !empty($exitsArray['d']) ? 'v' : '';

        $exits = $north . $east . $south . $west . $up . $down;

        $actorCondition = '';
        $opponentCondition = '';


        $actorConditionEstimateArray = Formulas::getConditionEstimate($character['HP'], $character['maxHP']);
        //todo нормальный schemeId
        $actorConditionClass = Constants::getConditionEstimateCssClass(1, $actorConditionEstimateArray['color_level']);

        if (!empty($character['opponent'])) {
            $actorConditionEstimate = "<span class={$actorConditionClass}>{$actorConditionEstimateArray['condition_estimate']}</span>";
            $actorCondition = "<span class='basic-color'>[{$character['name']}:</span>{$actorConditionEstimate}<span class='basic-color'>]&nbsp</span>";

            $opponentConditionEstimateArray = Formulas::getConditionEstimate($character['opponent']['HP'], $character['opponent']['maxHP']);
            //todo нормальный schemeId
            $opponentConditionClass = Constants::getConditionEstimateCssClass(1, $opponentConditionEstimateArray['color_level']);
            $opponentConditionEstimate = "<span class={$opponentConditionClass}>{$opponentConditionEstimateArray['condition_estimate']}</span>";
            $opponentCondition = "<span class='basic-color'>[{$character['opponent']['name']}:</span>{$opponentConditionEstimate}<span class='basic-color'>]&nbsp</span>";
        }

        return <<<STR
<span>
    <span class='{$actorConditionClass}'>{$character['HP']}H</span>
    <span class='health-good'>{$character['VP']}V</span>
    <span class='basic-color'>{$character['to_next_level']}X&nbsp{$character['coins']}C</span>
    {$actorCondition}{$opponentCondition}
    <span class='basic-color'>Вых:{$exits}></span>
</span>
<br>
STR;

    }


    public function renderRequestOnLook($character, $rooms)
    {
        $room = $rooms[$character['room_inner_id']];

        /**/
        Debugger::PrintToFile('--renderRequestOnLook--$room', $room);
        /**/

        $stateString = $this->renderStateString($character, $room['exits']);
        $roomName = "<span class='room-name'>" . $room['name'] . "</span><br>";
        $roomDescription = "<span class='basic-color'>" . $room['description'] . "</span><br>";

        $mobileTitle = '';
        if (!empty($room['mobiles'])) {
            foreach ($room['mobiles'] as $mobile) {
                if (!empty($mobile)) {
                    $mobileTitle .= "<span class='mobile-title'>" . $mobile['title_inside_of_room'] . "</span><br>";
                }
            }
            //чтобы отображение на клетке соответствовало порядку в массиве
//            foreach (array_reverse($room['mobiles']) as $mobile) {
//                if (!empty($mobile)) {
//                    $mobileTitle .= "<span class='mobile-title'>" . $mobile['title_inside_of_room'] . "</span><br>";
//                }
//            }
        }

        return $roomName . $roomDescription . $mobileTitle . $stateString;
    }

    public function renderRequestOnMove(&$character, $rooms, $stateString, $direction)
    {
        /**/
        if (!empty($character['opponent'])) {
//            return $stateString . "<span class='basic-color'>Не получится! Вы сражаетесь за свою жизнь!</span><br>";
            return "<span class='basic-color'>Не получится! Вы сражаетесь за свою жизнь!</span><br>" . $stateString;
        }
        /**/

        $nextRoomInnerId = !empty($rooms[$character['room_inner_id']]['exits'][$direction]) ? $rooms[$character['room_inner_id']]['exits'][$direction] : null;
        if ($nextRoomInnerId) {

            $character['room_inner_id'] = $nextRoomInnerId;
            $room = $rooms[$character['room_inner_id']];
            $stateString = $this->renderStateString($character, $rooms[$nextRoomInnerId]['exits']);
            $roomName = "<span class='room-name'>" . $rooms[$nextRoomInnerId]['name'] . "</span><br>";
            $mobileTitle = '';

            if (!empty($room['mobiles'])) {
//                foreach ($room['mobiles'] as $mobiles) {
//                    $mobileTitle .= "<span class='mobile-title'>" . $mobiles['title_inside_of_room'] . "</span>";
//                }
                //чтобы отображение на клетке соответствовало порядку в массиве
                foreach (array_reverse($room['mobiles']) as $mobile) {
//                    $mobileTitle .= "<span class='mobile-title'>" . $mobiles['title_inside_of_room'] . "</span>";
                    if (!empty($mobile)) {
                        $mobileTitle .= "<span class='mobile-title'>" . $mobile['title_inside_of_room'] . "</span><br>";
                    }
                }
            }

//            return $stateString . $mobileTitle . $roomName;
            return $roomName . $mobileTitle . $stateString;
        } else {
//            return $stateString . "<span class='basic-color'>Вы не можете идти в этом направлении...</span>";
            return "<span class='basic-color'>Вы не можете идти в этом направлении...</span><br>" . $stateString;
        }
    }

    public function renderSelectCharacterDialog($userEmailFromClient, $charName)
    {
        return <<<STR
<span class="basic-color">
Аккaунт [{$userEmailFromClient}] Персонаж [{$charName}]<br>
Добро пожаловать в MUD!<br>
0) Выход из MUDа.<br>
1) Начать игру.<br>
2) Ввести описание своего персонажа.(В разработке...)<br>
3) Прочитать начальную историю.(В разработке...)<br>
4) Поменять пароль.(В разработке...)<br>
5) Удалить этого персонажа.(В разработке...)<br>
--------------------------------<br>
В этом аккаунте вы также можете:<br>
6) Выбрать другого персонажа.<br>
7) Создать нового персонажа.<br>
8) Другие операции с аккаунтом.<br>
</span>
STR;

    }


    public function isItPossibleToTrain($character, $rooms)
    {
        $room = $rooms[$character['room_inner_id']];

        /**/
        Debugger::PrintToFile('--isItPossibleToTrain--$room', $room);
        /**/

//        $stateString = $this->renderStateString($character, $room['exits']);
//        $roomName = "<span class='room-name'>" . $room['name'] . "</span><br>";
//        $roomDescription = "<span class='basic-color'>" . $room['description'] . "</span><br>";

        $isItPossibleToTrain = 0;
        if (!empty($room['mobiles'])) {
            foreach ($room['mobiles'] as $mobile) {
                if (!empty($mobile) && !empty($mobile['teacher_level']) && $character['profession_id'] === $mobile['profession_id']) {
                    $isItPossibleToTrain = 1;
                }
                continue;
            }
        }

        return $isItPossibleToTrain;
    }


}
