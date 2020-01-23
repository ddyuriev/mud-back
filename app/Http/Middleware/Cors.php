<?php

namespace App\Http\Middleware;

use Closure;

class Cors {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next) {


        /**/
        $debugFile = 'debug/debug1111111-Cors.txt';
        file_exists($debugFile) ? $current = file_get_contents($debugFile) : $current = NULL;
        $results = print_r($request->all(), true);
        !empty($current) ? $current .= "\r\n" . $results : $current .= "\n" . $results;
        file_put_contents($debugFile, $current);
        /**/

        return $next($request)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Credentials', 'true')
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Version,Accept,Accept-Encoding,Accept-Language,Connection,Coockie,Authorization,DNT,X-CustomHeader,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type');
    }
}
