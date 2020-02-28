<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;
//use Tymon\JWTAuth\Contracts\Providers\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class Authenticate
{
    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory $auth
     *
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @param  string|null              $guard
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {

//        sleep(6);

        /**/
//        $debugFile = 'debug/debug1111111-Authenticate.txt';
//        file_exists($debugFile) ? $current = file_get_contents($debugFile) : $current = null;
//        $results = print_r($request->headers->all(), true);
//        !empty($current) ? $current .= "\r\n" . $results : $current .= "\n" . $results;
//        file_put_contents($debugFile, $current);
        /**/


//        if ($this->auth->guard($guard)->guest()) {
//            return response('Unauthorized.', 401);
//        }
//
//        return $next($request);


        /**/
        // caching the next action
//        $response = $next($request);

        //OK!!!
//        return $token = JWTAuth::getToken();
//        return JWTAuth::parseToken()->authenticate();
//        return JWTAuth::parseToken()->toUser();

        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (TokenInvalidException $e) {
            //токен не парсится. Требуется логинить заново
            return response()->json([
                'code'    => 1,
                'message' => $e->getMessage()
            ], 422);
        } catch (TokenExpiredException $e) {
            // If the token is expired, then it will be refreshed and added to the headers
            try {
                $refreshed = JWTAuth::refresh(JWTAuth::getToken());
            } catch (TokenBlacklistedException $e) {
                //токен попал в блеклист. Требуется логинить заново
                return response([
                    'code'    => 2,
                    'message' => $e->getMessage()
                ], 401);
            } catch (JWTException $e) {
                //тут врядли что будет, на всяк случай

                return response([
                    'code'    => 3,
                    'message' => $e->getMessage()
                ], 401);
            }

            JWTAuth::setToken($refreshed)->toUser();
            //возвращаем refreshed_token
            return response([
                'code'    => 0,
                'refreshed_token' => $refreshed
            ], 401);

        } catch (JWTException $e) {

            //тут врядли что будет, на всяк случай
            return response([
                'code'    => 4,
                'message' => $e->getMessage()
            ], 401);
        }

        /**/
        $debugFile = 'debug/debug1111111-sdfsfsfsdfs.txt';
        file_exists($debugFile) ? $current = file_get_contents($debugFile) : $current = null;
        $results = print_r('sdfsdfsdf', true);
        !empty($current) ? $current .= "\r\n" . $results : $current .= "\n" . $results;
        file_put_contents($debugFile, $current);
        /**/

        // Login the user instance for global usage
//        Auth::login($user, false);

//        $token = Auth::guard('api')->login($user);
//        $credentials = $request->only('email', 'password');
//        $this->guard()->attempt($credentials);

//        return $response;

        return $next($request);
        /**/
    }
}
