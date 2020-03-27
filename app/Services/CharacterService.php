<?php


namespace App\Services;

use App\Character;
use App\Helpers\Debugger;
use App\Helpers\Formulas;
use App\Slot;

class CharacterService
{
    public function getActiveCharacterByUserId($userId)
    {
        return Character::where('user_id', $userId)->where('is_active', 1)->first();
    }

    /**
     * @param $email
     *
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

        /*-----------OK - текущий...*/
//        $character = Character::with(['user' => function ($query) use ($email) {
//            $query->where('email', $email);
//        }, 'profession', 'skills.learning_level_check', 'stuff.slot'])->where('is_active', true)->first()->toArray();

//        $character = Character::with([
//            'user' => function ($query) use ($email) {
//                $query->where('email', $email);
//            },
//            'profession',
//            'skills.professions',
//            'stuff.slot'
//        ])->where('is_active', true)->first()->toArray();

        $character = Character::with([
            'user' => function ($query) use ($email) {
                $query->where('email', $email);
            },
            'profession.professionSkills',
//            'skills',
//            'skills.professions',
            'stuff.slot'
        ])->where('is_active', true)->first()->toArray();


//        $character['level'] = Formulas::calculateLevel($character['profession_id'], $character['experience']);

        $character['state'] = 1;

        $character['level'] = Formulas::calculateLevel($character['profession_id'], $character['experience']);
        $character['maxHP'] = Formulas::getMaxHP($character);

        $character['to_next_level'] = Formulas::toNextLevel($character['profession_id'], $character['experience'], $character['level']);

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

    /**
     * @param array $characterArray
     */
    public function updateCharacter(array $characterArray)
    {
        $character = Character::with('skills')->where('id', $characterArray['id'])->first();

        $data['experience'] = $characterArray['experience'];
        $data['HP'] = $characterArray['HP'];

        foreach ($data as $key => $parameter) {
            $character->$key = $parameter;
        }

        $character->save();
    }


    public function addingExperience(&$character, $experienceReward)
    {
        $level    = Formulas::calculateLevel($character['profession_id'], $character['experience']);
        $nexLevel = $level + 1;

//        $maxExpForCurrentLevel = (constant("SELF::WARRIOR_LEVEL_{$nexLevel}_EXP") - constant("SELF::WARRIOR_LEVEL_{$level}_EXP")) / 2;
        /**/
        $maxExpForCurrentLevel = (constant("App\Helpers\Formulas::WARRIOR_LEVEL_{$nexLevel}_EXP") - constant("App\Helpers\Formulas::WARRIOR_LEVEL_{$level}_EXP")) / 2;
        /**/

        if ($experienceReward > $maxExpForCurrentLevel) {
            $experienceReward = $maxExpForCurrentLevel;
        }

        $character['experience'] += $experienceReward;

        $newLevel = Formulas::calculateLevel($character['profession_id'], $character['experience']);

        $gotNewLevel = $newLevel - $level > 0 ? true : false;

        if ($gotNewLevel) {
            $character['maxHP']         = Formulas::getMaxHP($character);
            $character['level']         = $nexLevel;
            $character['to_next_level'] = Formulas::toNextLevel($character['profession_id'], $character['experience'], $newLevel);
        } else {
            $character['to_next_level'] -= $experienceReward;
        }

        return [
            'experienceReward' => $experienceReward,
            'got_new_level'    => $gotNewLevel
        ];
    }
}
