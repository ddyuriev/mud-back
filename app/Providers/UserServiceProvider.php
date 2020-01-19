<?php

namespace App\Providers;

use App\Services\UserService;
use App\SocketServer\Server;
use Illuminate\Support\ServiceProvider;

class UserServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
//        $this->app->singleton(Server::class, function ($app) {
//            $config = [
//                'host' => env("DB_HOST"),
//                'user' => env("DB_USERNAME"),
//                'password' => env("DB_PASSWORD"),
//                'dbname' => env("DB_DATABASE"),
//            ];
//            return new Server(new UserService(), $config);
//        });
    }
}
