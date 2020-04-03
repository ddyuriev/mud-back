<?php

namespace App\Http\Controllers;

use App\Helpers\Debugger;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Instantiate a new UserController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile(Request $request)
    {
//        sleep(7);

//        sleep(3);

        $user = Auth::user();

        /**/
//        Debugger::PrintToFile('--UserController', $user);
        /**/

        $localIp = $request->ip();

        if ($localIp == env("HOME_IP")) {
            $user->at_home = true;
        }


//        return response()->json(['user' => Auth::user()], 200);
        return response()->json(['user' => $user], 200);
    }

    /**
     * Get all User.
     *
     * @return Response
     */
    public function allUsers()
    {
        return response()->json(['users' => User::all()], 200);
    }

    /**
     * Get one user.
     *
     * @return Response
     */
    public function singleUser($id)
    {
        try {
            $user = User::findOrFail($id);

            return response()->json(['user' => $user], 200);

        } catch (\Exception $e) {

            return response()->json(['message' => 'user not found!'], 404);
        }

    }

}