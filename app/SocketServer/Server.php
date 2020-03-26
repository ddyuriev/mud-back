<?php

namespace App\SocketServer;

use App\Helpers\Constants;
use App\Helpers\Debugger;
use App\Helpers\Formulas;
use App\Http\Controllers\CharacterController;
use App\Jobs\SaveCharacterJob;
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
                /**/
                /**/
                Debugger::PrintToFile('--------------++++onConnect-$users', $users);
                /**/


                // а это сообщение будет отправлено клиенту
                $charName = !empty($activeCharacter) ? $activeCharacter['name'] : "Не выбран";

                $selectCharacterDialog = <<<STR
<span class="basic-color">
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

                $connection->send(json_encode(['selectCharacterDialog' => $selectCharacterDialog]));


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
//            Debugger::PrintToFile('-onMessage-$data', $data);
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
//            Debugger::PrintToFile('-+++++++onMessage++$character', $character);
            /**/

            switch (true) {
                /*---на 1-це---*/
                case $character['state'] == Constants::STATE_MENU && $data->message == 1:
                    $helloMessage = "<span class='basic-color'>Приветствуем вас на бескрайних просторах мира чудес и приключений!</span>";
                    $character['state'] = Constants::STATE_IN_GAME;
                    $character['room_inner_id'] = Room::START_ROOM_INNER_ID;
                    $stateString = $this->renderStateString($character, $rooms[Room::START_ROOM_INNER_ID]['exits']);
                    $roomName = "<span class='room-name'>" . $rooms[Room::START_ROOM_INNER_ID]['name'] . "</span>";
                    $connection->send(json_encode(['for_client' => $stateString . $roomName . $helloMessage]));

                    /**/
                    //тут мб таймер на восстановление хитов


                    $timerId = Timer::add($this->roundOfBattle, function () use ($connection, $rooms, &$character) {

                        /**/
//                        Debugger::PrintToFile('---на 1-це---', $character['HP']);
//                        Debugger::PrintToFile('---на 1-це---maxHP', $character['maxHP']);
                        /**/

                        if ($character['HP'] < $character['maxHP']) {
                            $character['HP']++;
                        }

                    });
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
<span class='basic-color'>Вы имеете <span class='{$conditionClass}'>{$currentHP}</span>(<span class='health-good'>{$maxHP}</span>) единиц здоровья.</span><br>
<span class='basic-color'>Вы набрали {$character['experience']} опыта и имеете </span><span style='color:gold'>{$character['coins']}</span><span class='basic-color'> монет.</span><br>
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
                case in_array($character['state'], [
                        Constants::STATE_IN_GAME,
                        Constants::STATE_IN_BATTLE
                    ]) && preg_match("/^осм(о)?(т)?(р)?(е)?(т)?(ь)?.*/", $data->message):
                    $dataMessage = $data->message;
                    $argument = mb_strtolower(trim(substr($dataMessage, strpos($dataMessage, ' '))));
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
                    if (!empty($character['profession']['profession_skills'])) {
                        foreach ($character['profession']['profession_skills'] as $skill) {
                            if ($character['level'] >= $skill['pivot']['learning_level']) {
                                $tableRows .= <<<STR
<tr>
  <th></th>
  <td width="30%">{$skill['name']}</td>
  <td>{$skill['character_skill']['value']}</td>
  <td></td>
</tr>
STR;
                            }
                        }
                    }

                    $skillsTable = <<<STR
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
<span style='color:gold'>Ваши умения:</span>
STR;
                    $connection->send(json_encode(['for_client' => $stateString . $skillsTable]));
                    break;

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
<span class='contrast-color'>Вы используете:</span>
STR;
                    $connection->send(json_encode(['for_client' => $stateString . $equipmentTable]));
                    break;


                //ударить
                case in_array($character['state'], [
                        Constants::STATE_IN_GAME,
                        Constants::STATE_IN_BATTLE
                    ])
//                    && preg_match("/^у(д)?(а)?(р)?(и)?(т)?(ь)?.*/", $data->message)
                    && preg_match("/^(у|уд|уда|удар|удари|ударит|ударить)\s.*/", $data->message):

                    $dataMessage = $data->message;
                    $argument = mb_strtolower(trim(substr($dataMessage, strpos($dataMessage, ' '))));
                    $room = $rooms[$character['room_inner_id']];

                    /**/
                    Debugger::PrintToFile('--Бой-$room', $room);
                    /**/

                    if (!empty($room['mobiles'])) {
                        for ($i = 0; $i <= count($room['mobiles']); $i++) {
                            if (!empty($room['mobiles'][$i])) {
                                foreach ($room['mobiles'][$i]['pseudonyms'] as $pseudonym) {
                                    if (mb_strtolower(trim(mb_substr($pseudonym, 0, mb_strlen($argument)))) == $argument) {
//                                    $character['opponent'] = &$mobile;
//                                    $character['opponent'] = &$room['mobiles'][$i];
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
                        $opponentMessage = "<span class='basic-color'>Похоже, здесь нет этого.</span>";
                        $connection->send(json_encode(['for_client' => $stateString . $opponentMessage]));
                        break;
                    }
                    //ставим режим "в бою"
                    $character['state'] = 3;
                    $faker = Factory::create();

                    $damage = $faker->numberBetween($character['first_damage_min'], $character['first_damage_max']);
                    if ($damage < $character['opponent']['HP']) {
                        $damageMessage = Formulas::damageMessage($damage);
                        $actorMessage = "<span class='actor-attack'>Вы $damageMessage рубанули {$character['opponent']['name']}. ($damage)</span>";

                        /**/
                        $character['opponent']['HP'] -= $damage;
                        /**/
                        $connection->send(json_encode(['for_client' => $this->renderStateString($character, $rooms[$character['room_inner_id']]['exits']) . $actorMessage]));

                        $timerId = Timer::add($this->roundOfBattle, function () use ($connection, $rooms, &$character, $faker) {
                            $actorDamage = $faker->numberBetween($character['first_damage_min'], $character['first_damage_max']);

                            if ($actorDamage < $character['opponent']['HP']) {
                                $damageMessage = Formulas::damageMessage($actorDamage);
                                $actorMessage = "<span class='actor-attack'>Вы $damageMessage рубанули {$character['opponent']['name']}. ($actorDamage)</span>";
                                $character['opponent']['HP'] -= $actorDamage;
//                                $opponentMessage             = "<span class='enemy-attack'>{$character['opponent']['name']} попытался огреть вас, но не смог этого сделать</span>";
                                $opponentMessage = '';
                                $opponentDamage = 0;
                                for ($i = 1; $i <= $character['opponent']['attacks_number']; $i++) {
                                    ${"opponentDamage{$i}"} = $faker->numberBetween($character['opponent']['damage_min'], $character['opponent']['damage_max']);
                                    ${"opponentMessage{$i}"} = Formulas::damageMessage(${"opponentDamage{$i}"});

                                    /**/
                                    Debugger::PrintToFile('--Бой-$opponentMessagei', ${"opponentMessage{$i}"});
                                    /**/
                                    $opponentDamage += ${"opponentDamage{$i}"};
                                    $opponentMessage .= "<span class='enemy-attack'>{$character['opponent']['name']} ${"opponentMessage{$i}"} ударил вас!</span>";
                                }

                                $character['HP'] -= $opponentDamage;

                                /**/
                                Debugger::PrintToFile('--Бой-$opponentMessage', $opponentMessage);
                                /**/

                                $connection->send(json_encode(['for_client' => $this->renderStateString($character, $rooms[$character['room_inner_id']]['exits']) . $opponentMessage . $actorMessage]));
                            } else {
                                Timer::del($character['timer_id']);
                                $character['state'] = 2;

                                $addingExperience = Formulas::addingExperience($character, $character['opponent']['exp_reward']);

//                                $newLevelMessage = !empty($addingExperience['got_new_level']) ? "<br><span class='contrast-color'>Вы поднялись на уровень!</span>" : '';

                                if (!empty($addingExperience['got_new_level'])) {
                                    $newLevelMessage = "<br><span class='contrast-color'>Вы поднялись на уровень!</span>";
                                    $character['maxHP'] = Formulas::getMaxHP($character);

                                } else {
                                    $newLevelMessage = "";
                                }

                                $actorMessage = <<<STR
<span>                                
    <span class='actor-attack'>Вы аккуратно разрезали {$this->strToLower($character['opponent']['name'])} на две части ($actorDamage)</span><br>
    <span class='basic-color'>{$character['opponent']['name']} мертв! R.I.P.</span><br>
    <span class='basic-color'>Вы получили {$addingExperience['experienceReward']} единиц опыта.</span>
    {$newLevelMessage}
</span>
STR;

                                /**/
                                //удалить моба
                                $character['opponent'] = null;
                                $connection->send(json_encode(['for_client' => $this->renderStateString($character, $rooms[$character['room_inner_id']]['exits']) . $actorMessage]));
                                dispatch(new SaveCharacterJob($character));
                            }

                        });

                        $character['timer_id'] = $timerId;
                    } else {
                        $character['state'] = 2;

                        $addingExperience = Formulas::addingExperience($character, $character['opponent']['exp_reward']);
                        $message = <<<STR
<span>
<span class='actor-attack'>Вы аккуратно разрезали {$this->strToLower($character['opponent']['name'])} на две части ($damage)</span>
<span class='basic-color'>{$character['opponent']['name']} мертв! R.I.P.</span><br>
<span class='basic-color'>Вы получили {$addingExperience['experienceReward']} единиц опыта.</span>
</span>
STR;
                        $character['opponent'] = null;
                        Timer::del($character['timer_id']);
                        $connection->send(json_encode(['for_client' => $stateString . $message]));
                    }


                    /**/
                    //???
//                    unset($character['opponent']);
                    /**/
                    break;

                case in_array($character['state'], [
                        Constants::STATE_IN_GAME,
                        Constants::STATE_IN_BATTLE
                    ]) && $data->message == 'empty_string':
                    $connection->send(json_encode(['for_client' => $stateString]));

                    break;

                /*---чисто в бою---*/
                case  $character['state'] == Constants::STATE_IN_BATTLE && $data->message == 'стоп':

                    Timer::del($character['timer_id']);
                    $character['state'] = 2;
                    unset($character['opponent']);

                    $connection->send(json_encode(['for_client' => $this->renderStateString($character, $rooms[$character['room_inner_id']]['exits']) . "<span class='contrast-color'>Вы решили остановить кровопролитие...</span>"]));

                    break;


                /**/

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
<div>
    <span class='{$actorConditionClass}'>{$character['HP']}H</span>
    <span class='health-good'>{$character['VP']}V</span>
    <span class='basic-color'>{$character['to_next_level']}X&nbsp{$character['coins']}C</span>
    {$actorCondition}{$opponentCondition}
    <span class='basic-color'>Вых:{$exits}></span>
</div>
STR;

    }


    public function renderRequestOnLook($character, $rooms)
    {
        $room = $rooms[$character['room_inner_id']];

        /**/
        Debugger::PrintToFile('--renderRequestOnLook--$room', $room);
        /**/

        $stateString = $this->renderStateString($character, $room['exits']);
        $roomName = "<span class='room-name'>" . $room['name'] . "</span>";
        $roomDescription = "<span class='basic-color'>" . $room['description'] . "</span>";

        $mobileTitle = '';
        if (!empty($room['mobiles'])) {
//            foreach ($room['mobiles'] as $mobiles) {
//                $mobileTitle .= "<span class='mobile-title'>" . $mobiles['title_inside_of_room'] . "</span>";
//            }
            //чтобы отображение на клетке соответствовало порядку в массиве
            foreach (array_reverse($room['mobiles']) as $mobiles) {
                $mobileTitle .= "<span class='mobile-title'>" . $mobiles['title_inside_of_room'] . "</span>";
            }
        }

        return $stateString . $mobileTitle . $roomDescription . $roomName;
    }

    public function renderRequestOnMove(&$character, $rooms, $stateString, $direction)
    {
        $nextRoomInnerId = !empty($rooms[$character['room_inner_id']]['exits'][$direction]) ? $rooms[$character['room_inner_id']]['exits'][$direction] : null;
        if ($nextRoomInnerId) {

            $character['room_inner_id'] = $nextRoomInnerId;
            $room = $rooms[$character['room_inner_id']];
            $stateString = $this->renderStateString($character, $rooms[$nextRoomInnerId]['exits']);
            $roomName = "<span class='room-name'>" . $rooms[$nextRoomInnerId]['name'] . "</span>";
            $mobileTitle = '';

            if (!empty($room['mobiles'])) {
//                foreach ($room['mobiles'] as $mobiles) {
//                    $mobileTitle .= "<span class='mobile-title'>" . $mobiles['title_inside_of_room'] . "</span>";
//                }
                //чтобы отображение на клетке соответствовало порядку в массиве
                foreach (array_reverse($room['mobiles']) as $mobiles) {
                    $mobileTitle .= "<span class='mobile-title'>" . $mobiles['title_inside_of_room'] . "</span>";
                }
            }

            return $stateString . $mobileTitle . $roomName;
        } else {
            return $stateString . "<span class='basic-color'>Вы не можете идти в этом направлении...</span>";
        }
    }
}
