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
        $debugFile = '_logs/debug1111111-index.txt';
        file_exists($debugFile) ? $current = file_get_contents($debugFile) : $current = NULL;
        $results = print_r('index', true);
        !empty($current) ? $current .= "\r\n" . $results : $current .= "\n" . $results;
        file_put_contents($debugFile, $current);
        /**/

        return Character::find(1);
    }

    public function userInput(Request $request)
    {

        /**/
        $debugFile = '_logs/debug1111111-userInput.txt';
        file_exists($debugFile) ? $current = file_get_contents($debugFile) : $current = NULL;
        $results = print_r($request->all(), true);
        !empty($current) ? $current .= "\r\n" . $results : $current .= "\n" . $results;
        file_put_contents($debugFile, $current);
        /**/

        return $request->all();
//        return Character::find(2);
    }
}
