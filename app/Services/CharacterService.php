<?php


namespace App\Services;

use App\Character;
use App\Helpers\Constants;
use App\Helpers\Debugger;
use App\Helpers\Formulas;
use App\Profession;
use App\Slot;
use Faker\Factory;

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

//        $character = Character::with([
//            'user' => function ($query) use ($email) {
//                $query->where('email', '=', $email);
//            },
//            'profession.professionSkills',
////            'skills',
////            'skills.professions',
//            'stuff.slot'
//        ])->where('is_active', true)->first()->toArray();


        /*2020.04.02*/

        //professionSkills - косяк
//        $character = Character::whereHas('user', function ($query) use ($email) {
//            $query->where('email', $email);
//        })->with([
//            'user',
//            'profession.professionSkills',
//            'stuff.slot'
//        ])->where('is_active', true)->first()->toArray();


        /*2020.04.03*/

        $character = Character::whereHas('user', function ($query) use ($email) {
            $query->where('email', $email);
        })->with([
            'user',
            'profession',
            'skills.professions',
            'stuff.slot'
        ])->where('is_active', true)->first()->toArray();

        //прическа скилов
        if (!empty($character['skills'])) {
            $skills = [];
            foreach ($character['skills'] as $skill) {
                $profession = array_filter($skill['professions'], function ($v) use ($character) {
                    return $v['pivot']['profession_id'] == $character['profession_id'];
                });
                if (!empty($profession)) {
                    $profession = array_shift($profession);
                    $learningLevel = $profession['pivot']['learning_level'];
                }
                $skills[] = [
                    'id' => $skill['id'],
                    'name' => $skill['name'],
                    'value' => $skill['pivot']['value'],
                    'learning_level' => $learningLevel,
                ];
            }
            $character['skills'] = $skills;
        }

//        dd($character);

//        \DB::enableQueryLog();
//        dd(\DB::getQueryLog(Character::whereHas('user', function ($query) use ($email) {
//            $query->where('email', $email);
//        })->with([
//            'user',
//            'profession',
//            'skills.professions',
//        ])->where('is_active', true)->first()));

//        \DB::enableQueryLog();
//        dd(\DB::getQueryLog(Character::whereHas('user', function ($query) use ($email) {
//            $query->where('email', $email);
//        })->with([
////            'user',
//            'profession.professionSkills',
////            'stuff.slot'
//        ])->where('is_active', true)->first()));


//        \DB::enableQueryLog();
//        dd(\DB::getQueryLog(Character::with([
//            'user' => function ($query) use ($email) {
//                $query->where('email', '=', $email);
//            }
//        ])->where('is_active', true)->first()));

        $character['state'] = Constants::STATE_MENU;

        $character['level'] = Formulas::calculateLevel($character['profession_id'], $character['experience']);
        $character['maxHP'] = Formulas::getMaxHP($character);

        $character['to_next_level'] = Formulas::toNextLevel($character['profession_id'], $character['experience'], $character['level']);


        $character['user_uuid'] = $character['user']['uuid'];
        $character['user_email'] = $character['user']['email'];
        unset($character['user']);

        //не нравится
//        foreach ($character['stuff'] as $item) {
//            if ($item['slot_id'] == $item['pivot']['slot_id'] && $item['slot_id'] == Slot::IN_BOTH_HANDS) {
//                $character['first_damage_min'] = $item['damage_min'];
//                $character['first_damage_max'] = $item['damage_max'];
//            } else if ($item['slot_id'] == $item['pivot']['slot_id'] && $item['slot_id'] == Slot::IN_RIGHT_HAND) {
//                $character['first_damage_min'] = $item['damage_min'];
//                $character['first_damage_max'] = $item['damage_max'];
//            } else if ($item['slot_id'] == $item['pivot']['slot_id'] && $item['slot_id'] == Slot::IN_LEFT_HAND) {
//                $character['second_damage_min'] = $item['damage_min'];
//                $character['second_damage_max'] = $item['damage_max'];
//            }
//        }

        /**/
        $this->setDamageRange($character);
//        dd($fh);
        /**/

        return $character;
    }

    /**
     * @param array $characterArray
     */
    public function updateCharacter(array $characterArray)
    {
        $character = Character::with('skills')->where('id', $characterArray['id'])->first();

//        $data['experience'] = $characterArray['experience'];
//        $data['HP'] = $characterArray['HP'];


        $fieldsArray = [
            'strength',
            'dexterity',
            'constitution',
            'intellect',
            'wisdom',
            'resistance',
            'experience',
            'HP',
            'training_level'
        ];
        //кривовато
        foreach ($fieldsArray as $parameter) {
            $data[$parameter] = $characterArray[$parameter];
        }
        foreach ($data as $key => $parameter) {
            $character->$key = $parameter;
        }

        $character->save();
    }

    public function addingExperience(&$character, $experienceReward)
    {
        $level = Formulas::calculateLevel($character['profession_id'], $character['experience']);
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
            $character['maxHP'] = Formulas::getMaxHP($character);
            $character['level'] = $nexLevel;
            $character['to_next_level'] = Formulas::toNextLevel($character['profession_id'], $character['experience'], $newLevel);
        } else {
            $character['to_next_level'] -= $experienceReward;
        }

        return [
            'experienceReward' => $experienceReward,
            'got_new_level' => $gotNewLevel
        ];
    }

    public function createCharacter($data)
    {
        /**/
        Debugger::PrintToFile('CharacterService--createCharacterr', 'createCharacter');
        /**/

        $character = new Character();

        $character->user_id = $data['user_id'];
        $character->name = $data['name'];

        Character::where('user_id', $data['user_id'])->where('is_active', true)->update(['is_active' => false]);

        $character->profession_id = $data['profession_id'];
        $character->experience = 1;
        $character->strength = 8;
        $character->dexterity = 8;
        $character->constitution = 8;
        $character->intellect = 8;
        $character->wisdom = 8;
        $character->resistance = 8;
        $character->VP = 70;
        $character->coins = 0;
        $character->delevels_count = 0;
        $character->is_active = 1;

        switch ($character->profession_id) {
            case Profession::WARRIOR_ID:
                $character->constitution = $character->constitution + 2;
                $character->strength = $character->strength + 1;
                break;
            case Profession::WIZARD_ID:
                $character->intellect = $character->intellect + 2;
                $character->wisdom = $character->wisdom + 1;
                break;
        }

        $firstLevelUpCharacteristic = $this->getLevelUpCharacteristic($character->profession_id);

        switch ($firstLevelUpCharacteristic) {
            case Constants::STRENGTH:
                $character->strength = $character->strength + 1;
                break;
            case Constants::DEXTERITY:
                $character->dexterity++;
                break;
            case Constants::CONSTITUTION:
                $character->constitution++;
                break;
            case Constants::INTELLECT:
                $character->intellect++;
                break;
            case Constants::WISDOM:
                $character->wisdom++;
                break;
            case Constants::RESISTANCE:
                $character->resistance++;
                break;
        }

        $character->HP = Formulas::getMaxHP(['profession_id' => 1, 'constitution' => $character->constitution, 'level' => 1]);

        $character->save();

        $newLevel = Formulas::calculateLevel($character['profession_id'], $character['experience']);

        $character['to_next_level'] = Formulas::toNextLevel($character['profession_id'], $character['experience'], $newLevel);
        $character['state'] = Constants::STATE_MENU;
        $character['level'] = Formulas::calculateLevel($character['profession_id'], $character['experience']);
        $character['maxHP'] = Formulas::getMaxHP($character);

        $character->load('profession', 'user', 'skills', 'stuff');

        $character = $character->toArray();

        $this->setDamageRange($character);

        return $character;
    }

    public function getInventoryItems($character)
    {
        return array_filter($character['stuff'], function ($v) {
            return $v['pivot']['slot_id'] == Slot::IN_INVENTORY;
        });
    }

    public function setDamageRange(&$character)
    {
        $bothHandsWeapon = array_filter($character['stuff'], function ($v) {
            return $v['pivot']['slot_id'] == Slot::IN_BOTH_HANDS;
        });
        $bothHandsWeapon = array_shift($bothHandsWeapon);

        $rightHandWeapon = array_filter($character['stuff'], function ($v) {
            return $v['pivot']['slot_id'] == Slot::IN_RIGHT_HAND;
        });
        $rightHandWeapon = array_shift($rightHandWeapon);

        $leftHandWeapon = array_filter($character['stuff'], function ($v) {
            return $v['pivot']['slot_id'] == Slot::IN_LEFT_HAND;
        });
        $leftHandWeapon = array_shift($leftHandWeapon);

        if (!empty($bothHandsWeapon)) {
            $character['first_damage_min'] = $bothHandsWeapon['damage_min'];
            $character['first_damage_max'] = $bothHandsWeapon['damage_max'];
        } elseif (!empty($rightHandWeapon)) {
            $character['first_damage_min'] = $rightHandWeapon['damage_min'];
            $character['first_damage_max'] = $rightHandWeapon['damage_max'];
        } else {
            $character['first_damage_min'] = 1;
            $character['first_damage_max'] = 1;
        }
        if (!empty($leftHandWeapon)) {
            $character['second_damage_min'] = $leftHandWeapon['damage_min'];
            $character['second_damage_max'] = $leftHandWeapon['damage_max'];
        }
    }

    public function getLevelUpCharacteristic($professionId)
    {
        $faker = Factory::create();
        $number = $faker->numberBetween(1, 600);
        $parameter = 0;

        switch ($professionId) {
            case Profession::WARRIOR_ID:
                switch ($number) {
                    case $number <= 110:
                        $parameter = Constants::STRENGTH;
                        break;
                    case $number > 110 && $number <= 220:
                        $parameter = Constants::DEXTERITY;
                        break;
                    case $number > 220 && $number <= 330:
                        $parameter = Constants::CONSTITUTION;
                        break;
                    case $number > 330 && $number <= 415:
                        $parameter = Constants::INTELLECT;
                        break;
                    case $number > 415 && $number <= 500:
                        $parameter = Constants::WISDOM;
                        break;
                    case $number > 500 && $number <= 600:
                        $parameter = Constants::RESISTANCE;
                        break;
                }
                break;
            case Profession::WIZARD_ID:
                switch ($number) {
                    case $number <= 100:
                        $parameter = Constants::STRENGTH;
                        break;
                    case $number > 100 && $number <= 200:
                        $parameter = Constants::DEXTERITY;
                        break;
                    case $number > 200 && $number <= 300:
                        $parameter = Constants::CONSTITUTION;
                        break;
                    case $number > 300 && $number <= 400:
                        $parameter = Constants::INTELLECT;
                        break;
                    case $number > 400 && $number <= 500:
                        $parameter = Constants::WISDOM;
                        break;
                    case $number > 500 && $number <= 600:
                        $parameter = Constants::RESISTANCE;
                        break;
                }
                break;
        }
        return $parameter;
    }

    public function setLevelUpCharacteristic(&$character)
    {
        //до 25 уровня включительно начисляется характеристка
//        if ($character->level <= 25 && $character->level > $character->training_level) {
//            $levelUpCharacteristic = $this->getLevelUpCharacteristic($character->professionId);
//            $characteristicName = Constants::getCharacteristicNameByConstant($levelUpCharacteristic);
//            $character->$characteristicName++;
//        }
        if ($character['level'] <= 25 && $character['level'] > $character['training_level']) {
            $levelUpCharacteristic = $this->getLevelUpCharacteristic($character['profession_id']);
            $characteristicName = Constants::getCharacteristicNameByConstant($levelUpCharacteristic);
            $character[$characteristicName]++;
            $character['training_level'] = $character['level'];
        }
        $character['maxHP'] = Formulas::getMaxHP($character);
    }


}
