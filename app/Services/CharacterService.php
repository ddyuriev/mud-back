<?php


namespace App\Services;

use App\Character;

class CharacterService
{
    public function getActiveCharacterByUserId($userId)
    {
        return Character::where('user_id', $userId)->where('is_active', 1)->first();
    }

    public function getActiveCharacterByUserEmail($email)
    {
//        return Character::with('user')->where('user.email', $email)->first();

//        return Character::whereHas('user', function ($query) use ($email) {
//            $query->where('email', $email);
//        })->where('is_active', true)->first();

//        return Character::with('user')->whereHas('user', function ($query) use ($email) {
//            $query->where('email', $email);
//        })->where('is_active', true)->first();

        $character= Character::with(['user' => function ($query) use ($email) {
            $query->where('email', $email);
        }])->first();

//        $character->user_uuid = $character->

        return $character;
    }
}
