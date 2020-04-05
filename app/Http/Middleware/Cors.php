<?php

namespace App\Http\Middleware;

use App\Helpers\Debugger;
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
        Debugger::PrintToFile('Cors', $request->all());
        /**/

        return $next($request)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Credentials', 'true')
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Version,Accept,Accept-Encoding,Accept-Language,Connection,Coockie,Authorization,DNT,X-CustomHeader,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type');
    }
}
