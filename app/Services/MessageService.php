<?php


namespace App\Services;

use App\Message;

class MessageService
{
    public function create($data)
    {
        $message = new Message();
        foreach ($data as $key => $value) {
            $message[$key] = $value;
        }
        $message->save();
        return $message;
    }

    /**/
    public function selectWithUser()
    {
//        return Message::with('user')->get();

//        $messages = DB::table('message')
//            ->leftJoin('users', 'message.uniqueId', '=', 'users.uniqueId')
//            ->select('message.*', 'contacts.phone', 'orders.price')
//            ->get();

        return \DB::table('users')
            ->leftJoin('message', 'users.uniqueId', '=', 'message.uniqueId')
            ->select('users.name', 'users.color', 'message.time', 'message.message')
            ->get();
    }
    /**/
}
