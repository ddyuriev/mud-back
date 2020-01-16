<?php

require_once __DIR__ . '/../vendor/autoload.php';
use Workerman\Worker;

// массив для связи соединения пользователя и необходимого нам параметра
$users = [];

// создаём ws-сервер, к которому будут подключаться все наши пользователи
$ws_worker = new Worker("websocket://0.0.0.0:8000");
// создаём обработчик, который будет выполняться при запуске ws-сервера
$ws_worker->onWorkerStart = function() use (&$users)
{
    // создаём локальный tcp-сервер, чтобы отправлять на него сообщения из кода нашего сайта
    $inner_tcp_worker = new Worker("tcp://127.0.0.1:1234");
    // создаём обработчик сообщений, который будет срабатывать,
    // когда на локальный tcp-сокет приходит сообщение
    $inner_tcp_worker->onMessage = function($connection, $data) use (&$users) {

        /**/
        $debugFile = '_logs/debug1111111-$users.txt';
        file_exists($debugFile) ? $current = file_get_contents($debugFile) : $current = NULL;
        $results = print_r($users, true);
        !empty($current) ? $current .= "\r\n" . $results : $current .= "\n" . $results;
        file_put_contents($debugFile, $current);
        /**/



        $data = json_decode($data);
        // отправляем сообщение пользователю по userId


        /**/
        $debugFile = '_logs/debug1111111-$data.txt';
        file_exists($debugFile) ? $current = file_get_contents($debugFile) : $current = NULL;
        $results = print_r($data, true);
        !empty($current) ? $current .= "\r\n" . $results : $current .= "\n" . $results;
        file_put_contents($debugFile, $current);
        /**/

        if (isset($users[$data->user])) {

            /**/
            $debugFile = '_logs/debug1111111-$users-222.txt';
            file_exists($debugFile) ? $current = file_get_contents($debugFile) : $current = NULL;
            $results = print_r($users, true);
            !empty($current) ? $current .= "\r\n" . $results : $current .= "\n" . $results;
            file_put_contents($debugFile, $current);
            /**/

            $webConnection = $users[$data->user];
            $webConnection->send($data->message);
        }
    };
    $inner_tcp_worker->listen();
};

$ws_worker->onConnect = function($connection) use (&$users)
{
    $connection->onWebSocketConnect = function($connection) use (&$users)
    {
        // при подключении нового пользователя сохраняем get-параметр, который же сами и передали со страницы сайта
        $users[$_GET['user']] = $connection;
        // вместо get-параметра можно также использовать параметр из cookie, например $_COOKIE['PHPSESSID']
    };
};

$ws_worker->onClose = function($connection) use(&$users)
{

    /**/
    $debugFile = '_logs/debug1111111-onClose-$users.txt';
    file_exists($debugFile) ? $current = file_get_contents($debugFile) : $current = NULL;
    $results = print_r($users, true);
    !empty($current) ? $current .= "\r\n" . $results : $current .= "\n" . $results;
    file_put_contents($debugFile, $current);
    /**/

    // удаляем параметр при отключении пользователя
    $user = array_search($connection, $users);
    unset($users[$user]);
};

// Run worker
Worker::runAll();