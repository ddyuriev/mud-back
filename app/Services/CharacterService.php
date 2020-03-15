<?php


namespace App\Services;

use App\Character;
use App\Helpers\Debugger;
use App\Slot;

class CharacterService
{
    public function getActiveCharacterByUserId($userId)
    {
        return Character::where('user_id', $userId)->where('is_active', 1)->first();
    }

    /**
     * @param $email
     * @return array
     */
    public function getActiveCharacterByUserEmail($email)
    {
//        return Character::with('user')->where('user.email', $email)->first();

//        return Character::whereHas('user', function ($query) use ($email) {
//            $query->where('email', $email);
//        })->where('is_active', true)->first();

//        return Character::with('user')->whereHas('user', function ($query) use ($email) {
//            $query->where('email', $email);
//        })->where('is_active', true)->first();


//        $character = Character::with(['user' => function ($query) use ($email) {
//            $query->where('email', $email);
//        }, 'profession', 'skills'])->where('is_active', true)->first();

        //OK
//        $character = Character::with(['user' => function ($query) use ($email) {
//            $query->where('email', $email);
//        }, 'profession', 'skills.learning_level_check'])->where('is_active', true)->first();

//        $character = Character::with(['user' => function ($query) use ($email) {
//            $query->where('email', $email);
//        }, 'profession', 'skills.learning_level_check', 'stuff'])->where('is_active', true)->first();

        $character = Character::with(['user' => function ($query) use ($email) {
            $query->where('email', $email);
        }, 'profession', 'skills.learning_level_check', 'stuff.slot'])->where('is_active', true)->first()->toArray();


        foreach ($character['stuff'] as $item) {

            if ($item['slot_id'] == $item['pivot']['slot_id'] && $item['slot_id'] == Slot::IN_BOTH_HANDS) {
                $character['first_damage_min'] = $item['damage_min'];
                $character['first_damage_max'] = $item['damage_max'];
            } else if ($item['slot_id'] == $item['pivot']['slot_id'] && $item['slot_id'] == Slot::IN_RIGHT_HAND) {
                $character['first_damage_min'] = $item['damage_min'];
                $character['first_damage_max'] = $item['damage_max'];
            } else if ($item['slot_id'] == $item['pivot']['slot_id'] && $item['slot_id'] == Slot::IN_LEFT_HAND) {
                $character['second_damage_min'] = $item['damage_min'];
                $character['second_damage_max'] = $item['damage_max'];
            }

        }

        return $character;
    }
}
