<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
//    return $router->app->version();

//    return time();


//    $message = 'Время жизни вселенной: ';
//    $message.= '1';
//
//    return $message;


//    $users = \App\User::All();

    $messageService = new \App\Services\MessageService();

    $users = $messageService->selectWithUser();

    return $users;



});


//$router->get('/', 'CharacterController@index')->middleware('cors');

//$router->get('/', ['middleware' => 'cors', function () use ($router) {
//    return $router->app->version();
//}]);


//$router->get('/', /*['middleware' => 'cors'], */'CharacterController@index');

//$router->get('/', ['middleware' => 'cors', 'uses' => 'CharacterController@index']);


//$router->post('/userinput', 'CharacterController@userInput')->middleware('cors');


$router->post('/userinput', ['middleware' => 'cors', 'uses' => 'CharacterController@userInput']);