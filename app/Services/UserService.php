<?php


namespace App\Services;


use App\User;

class UserService
{

    public function create($data)
    {
        $user = new User();

        foreach ($data as $key => $value) {
            $user[$key] = $value;
        }
        $user->save();

        return $user;
    }

    /**
     * @param $uniqueId
     *
     * @return mixed
     */
    public function findByUuid($uuid)
    {
//        return User::where('unique_id', $uniqueId)->first();
        return User::where('uuid', $uuid)->first();
    }

    public function findByEmail($email)
    {
        return User::where('email', $email)->first();
    }

}
