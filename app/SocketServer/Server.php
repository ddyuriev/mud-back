<?php

namespace App\SocketServer;

//require_once __DIR__ . '../../vendor/autoload.php';
//require_once __DIR__ . '/config.php';

//require_once __DIR__ . '/config.php';
//require_once __DIR__ . '/../vendor/autoload.php';

use Workerman\Worker;

//use Logger;

//use Workerman\Worker;
//use Server\Mysql;
//use Server\Logger;
//use Server\DataInterface;
//use Server\LoggerInterface;
//use Workerman\Lib\Timer;

//use SocketServer\Logger;


use Server\DataInterface;
use Server\LoggerInterface;

class Server
{
//    private $users;
//    private $db;

//    public function __construct($config, DataInterface $db, LoggerInterface $logger)
//    {
//        $this->ws_worker        = new Worker("websocket://$config[host]:$config[port]");
//        $this->db               = $db;
//        $this->logger           = $logger;
//        $this->ws_worker->count = $config['countWorkers'];
//        $this->config           = $config;
//    }

    public function __construct($config/*, DataInterface $db*/, LoggerInterface $logger)
    {
//        $this->ws_worker        = new Worker("websocket://$config[host]:$config[port]");
        /**/
        $this->ws_worker        = new Worker("websocket://$config[host]:$config[port]");
        /**/
//        $this->db               = $db;
        $this->logger           = $logger;
//        $this->ws_worker->count = $config['countWorkers'];
//        $this->config           = $config;

//        \Server\Logger::class;
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
                $result = $this->db->ping();
                $this->logger->save(date("H:i:s"), 'Service', $result);
            });
        };

        /*
        * При получении сообщения записываем его в БД и рассылаем пользователям
        *
        */
        $this->ws_worker->onMessage = function ($connection, $data) use (&$users) {
            $data       = json_decode($data);
            $time       = date("H:i:s");
            $data->time = $time;

//            $findUser = $this->db->select('users', 'uniqueId', null, 'uniqueId', $data->uniqueId);  // ищем пользователя по уникальному ИД в базе
//
//            if ($findUser) {
//                $result = $this->db->save('message', ['time', 'message', 'uniqueId'], [
//                    $time,
//                    $data->message,
//                    $data->uniqueId
//                ]); // если пользователь был найден - сохраняем в связанную таблицу его сообщение
//                $this->logger->save(date("H:i:s"), 'Service', $result);
//            } else {
//                $result = $this->db->save('users', ['uniqueId', 'name', 'color'], [
//                    $data->uniqueId,
//                    $data->name,
//                    $data->userColor
//                ]); // если такого пользователя нет - записываем его в БД и сохраняем в связанную таблицу его сообщение
//                $this->logger->save(date("H:i:s"), 'Service', $result);
//                $result = $this->db->save('message', ['time', 'message', 'uniqueId'], [
//                    $time,
//                    $data->message,
//                    $data->uniqueId
//                ]);
//                $this->logger->save(date("H:i:s"), 'Service', $result);
//            }

            foreach ($this->users as $value) {
                $value->send(json_encode($data));
            }
        };

        /*
        * При новом подключении уведомляем пользователей, достаем старые сообщения, пишем в лог
        *
        */
        $this->ws_worker->onConnect = function ($connection) use (&$users) {
            $connection->onWebSocketConnect = function ($connection) use (&$users) {

//                $this->logger->save(date("H:i:s"), 'Service', 'Пользователь присоединился');
//                $result = $this->db->select('users us', 'name, color, time, message', 'message me on us.uniqueId = me.uniqueId'); // достаем из БД все сообщения пользователей
//
//                $this->users[$connection->id] = $connection;
//
//                if (!is_array($result)) {
//                    $this->logger->save(date("H:i:s"), 'Service', $result); // если пришел не массив - ошибка при запросе
//                } else {
//                    $result = json_encode(['dialog' => $result]);
//                    $this->users[$connection->id]->send($result);
//                }

                foreach ($this->users as $value) {
                    $service = json_encode(['service' => 'Пользователь присоединился.']);
                    $value->send($service);
                }
            };
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

//$db     = new Mysql($config['db']);
//$log    = new Logger($config['log']);
/**/
//$log    = new \Server\Logger($config['log']);
//$log    = new Logger($config['log']);
//$log    = new \SocketServer\Logger($config['log']);
/**/


//$log    = new Logger($config['log']);
//$log    = new Logger();


//$server = new Server2($config['webSocket'], /*$db,*/ $log);
//$server->serverStart();


//$db     = new Mysql($config['db']);
//$log    = new Logger($config['log']);
//$server = new Server2($config['webSocket'], /*$db,*/ $log);
//$server->serverStart();
