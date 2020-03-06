<?php

namespace App\Http\Controllers;

use App\Character;
use App\Helpers\Debugger;
use Illuminate\Http\Request;

class CharacterController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }


    public function index()
    {
        /**/
        $debugFile = 'debug1111111-index.txt';
        file_exists($debugFile) ? $current = file_get_contents($debugFile) : $current = null;
        $results = print_r('index', true);
        !empty($current) ? $current .= "\r\n" . $results : $current .= "\n" . $results;
        file_put_contents($debugFile, $current);

        /**/

        return Character::find(1);
    }

    public function userInput(Request $request)
    {

        /*-----------------------------------*/
        $arr = array('x', 'y', 'z');
//        To insert this into a database field you can use serialize function like this
        $serializedArr = serialize($arr);

//        print_r($serializedArr);
//        dd($serializedArr);
//        dd($arr);

        $character = Character::find(1);


        Debugger::PrintToFile('$character', $character);

        $name = $character->name;
//        dd($character);

        print_r($name);

//        $character->name = $serializedArr;
//        $character->save();

        /*-----------------------------------*/


        /*-----------------------------------*/
//        $character = Character::find(1);
//        $character->room_uuid = 999999999;
//        dd($character->room_uuid);
//        dd($character['room_uuid']);
        /*-----------------------------------*/


        /*-----------------------------------*/
//        $arr = [
//            10 => 1,
//            20 => 2
//        ];
//        $x = &$arr[10];
//        $x = 1000;
//        return $arr;
        /*-----------------------------------*/


//        Debugger::PrintToFile('zzzzzz', $request->all());
//        exit();

        /*-----------------------------------*/
        return \Illuminate\Support\Str::uuid()->toString();
        /*-----------------------------------*/

        //OK
//        return  $request->ip();

//        return $request;

//        $request->ip()


        /**/
//        $debugFile = 'debug1111111-userInput.txt';
//        file_exists($debugFile) ? $current = file_get_contents($debugFile) : $current = null;
//        $results = print_r($request->all(), true);
//        !empty($current) ? $current .= "\r\n" . $results : $current .= "\n" . $results;
//        file_put_contents($debugFile, $current);
        /**/

//        $email = 'therion@mail.ru';
        $email = 'dimas@mail.ru';

//        $result = Character::with(['user' => function ($query) use ($email) {
//            $query->where('email', $email);
//        }])->where('is_active', true)->get();

//        $result = Character::whereHas('user', function ($query) {
//            $query->where('email', 'therion@mail.ru');
//        })->where('is_active', true)->first();


        //косяк
//        $result = Character::with([
//            'user' => function ($query) use ($email) {
//                $query->where('user.email', 'dimas@mail.ru');
//            },
//            'profession'
//        ])->first();


        $result = Character::with('user', 'profession')->whereHas('user', function ($query) use ($email) {
            $query->where('email', $email);
        })->where('is_active', true)->toSql();

        /**/
        $debugFile = 'debug1111111-userInput.txt';
        file_exists($debugFile) ? $current = file_get_contents($debugFile) : $current = null;
        $results = print_r($result, true);
        !empty($current) ? $current .= "\r\n" . $results : $current .= "\n" . $results;
        file_put_contents($debugFile, $current);

        /**/

        return $result;
    }


}
