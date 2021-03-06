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

//$router->get('/api/profile', ['middleware' => 'cors', 'uses' => 'UserController@profile']);


// API route group
$router->group(['prefix' => 'api', 'middleware' => 'cors'], function () use ($router) {
    // Matches "/api/register
    $router->post('register', 'AuthController@register');

    // Matches "/api/login
    $router->post('login', 'AuthController@login');

    // Matches "/api/profile
    $router->get('profile', 'UserController@profile');
//    $router->get('profile', ['middleware' => 'cors', 'uses' => 'UserController@profile']);

    // Matches "/api/users/1
    //get one user by id
    $router->get('users/{id}', 'UserController@singleUser');

    // Matches "/api/users
    $router->get('users', 'UserController@allUsers');
});