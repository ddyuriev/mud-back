<?php

namespace App\Http\Controllers;

use App\Character;
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

        return \Illuminate\Support\Str::uuid()->toString();
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
