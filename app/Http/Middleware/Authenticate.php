<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;
//use Tymon\JWTAuth\Contracts\Providers\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
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
     * @param  \Closure $next
     * @param  string|null $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        /**/
        $debugFile = 'debug/debug1111111-Authenticate.txt';
        file_exists($debugFile) ? $current = file_get_contents($debugFile) : $current = NULL;
        $results = print_r($request->headers->all(), true);
        !empty($current) ? $current .= "\r\n" . $results : $current .= "\n" . $results;
        file_put_contents($debugFile, $current);
        /**/


//        if ($this->auth->guard($guard)->guest()) {
//            return response('Unauthorized.', 401);
//        }
//
//        return $next($request);


        /**/
        // caching the next action
        $response = $next($request);

        //OK!!!
//        return $token = JWTAuth::getToken();
//        return JWTAuth::parseToken()->authenticate();
//        return JWTAuth::parseToken()->toUser();

        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response('Unauthorized.', 401);
            }

            //        if ($this->auth->guard($guard)->guest()) {
//            return response('Unauthorized.', 401);
//        }
        } catch (TokenInvalidException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (TokenExpiredException $e) {

//            return response('Unau++++++thorized.', 401);

            // If the token is expired, then it will be refreshed and added to the headers
            try {
                $refreshed = JWTAuth::refresh(JWTAuth::getToken());

//                return $refreshed;

                $response->header('Authorization', 'Bearer ' . $refreshed);
            } catch (JWTException $e) {
//                return ApiHelpers::ApiResponse(103, null);
//                return response($e->getMessage(), 103);
                return response()->json(['message' => $e->getMessage()], 103);
            }
            $user = JWTAuth::setToken($refreshed)->toUser();
        } catch (JWTException $e) {
            return response($e->getMessage(), 101);
        }

        // Login the user instance for global usage
//        Auth::login($user, false);

//        $token = Auth::guard('api')->login($user);
//        $credentials = $request->only('email', 'password');
//        $this->guard()->attempt($credentials);

        return $response;
        /**/
    }
}
