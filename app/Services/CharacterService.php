<?php


namespace App\Services;

use App\Character;
use App\Helpers\Constants;
use App\Helpers\Debugger;
use App\Helpers\Formulas;
use App\Jobs\SaveCharacterJob;
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
            'stuff.dest_slot'
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

        /**/
        if (!empty($characterArray['parameters_increase'])) {
//            $character['parameters_increase'] = json_encode($characterArray['parameters_increase']);
            $character['parameters_increase'] = $characterArray['parameters_increase'];
        }
        /**/
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
        $character->trainings_count = 25;

        switch ($character->profession_id) {
            case Profession::WARRIOR_ID:
                $character->strength = $character->strength + 2;
                $character->constitution = $character->constitution + 1;
                break;
            case Profession::WIZARD_ID:
                $character->intellect = $character->intellect + 2;
                $character->wisdom = $character->wisdom + 1;
                break;
        }

//        $firstLevelUpCharacteristic = $this->getLevelUpCharacteristic($character->profession_id);
        $firstLevelUpCharacteristic = Formulas::getLevelUpCharacteristic($character->profession_id);

        switch ($firstLevelUpCharacteristic) {
            case Constants::STRENGTH:
                $character->strength++;
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

    public function getClothedItems($character)
    {
        return array_filter($character['stuff'], function ($v) {
            return $v['pivot']['slot_id'] != Slot::IN_INVENTORY;
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
            $character['first_damage_min'] = $rightHandWeapon['damage_min'] + Formulas::strengthDamageBonus($character['strength']);
            $character['first_damage_max'] = $rightHandWeapon['damage_max'] + Formulas::strengthDamageBonus($character['strength']);
        } else {
            $character['first_damage_min'] = 1;
            $character['first_damage_max'] = 1;
        }
        if (!empty($leftHandWeapon)) {
            $character['second_damage_min'] = $leftHandWeapon['damage_min'];
            $character['second_damage_max'] = $leftHandWeapon['damage_max'];
        }
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
            $levelUpCharacteristic = Formulas::getLevelUpCharacteristic($character['profession_id']);
            $characteristicName = Constants::getCharacteristicNameByConstant($levelUpCharacteristic);
            $character[$characteristicName]++;
            $character['training_level'] = $character['level'];
        }
        $character['maxHP'] = Formulas::getMaxHP($character);
    }

    public function increaseCharacteristic(&$character, $characteristicName)
    {
        echo $characteristicName . "\r\n";

        echo mb_strlen($characteristicName) . "\r\n";

//        dd(mb_strlen($characteristicName) < 3);

        echo mb_strpos('тренировать', $characteristicName) . "\r\n";

//        dd(mb_strpos('тренировать', $characteristicName) === 0);

//        dd($character['parameters_increase']['strength']);

        $increaseStrangePossibility = !empty($character['parameters_increase']['strength']) ? 5 - $character['parameters_increase']['strength'] : 5;


        //если меньше трех символов аргумент или ввод без пробела
        if (mb_strlen($characteristicName) < 3 || mb_strpos('тренировать', $characteristicName) === 0) {
            return <<<STR
Вы можете тренировать:<br>
 силу         : {$increaseStrangePossibility}<br>
 интеллект    : 0 из 5<br>
 мудрость     : 0 из 5<br>
 телосложение : 0 из 5<br>
 ловкость     : 0 из 5<br>
 стойкость    : 0 из 5<br>
Цена поднятия характеристики на единицу - 1 тренировка<br>
У вас в наличие {$character['trainings_count']} тренировок
STR;
        }

        $characteristicId = Formulas::getCharacteristicByName($characteristicName);
        $characteristicFullName = Constants::getCharacteristicNameByConstant($characteristicId);

        if ($character['trainings_count'] > 0 && $characteristicId > 0) {

            switch (true) {
                case $characteristicId == Constants::STRENGTH:
                    $character['strength']++;
                    $this->setDamageRange($character);
//                    !empty($character['parameters_increase']['strength']) ? $character['parameters_increase']['strength']++ : $character['parameters_increase']['strength'] = 1;
//                    !empty($character['parameters_increase']['strength']) ? $character['parameters_increase']['strength']++ : $c = 1;
                    !empty($character['parameters_increase']['strength']) ? $character['parameters_increase']['strength']++ : $character['parameters_increase']['strength'] = 1;
                    /**/
//                    dispatch(new SaveCharacterJob($character));
                    /**/
                    break;
                case $characteristicId == Constants::DEXTERITY:
                    $character['dexterity']++;
                    break;
                case $characteristicId == Constants::CONSTITUTION:
                    $character['constitution']++;
                    $character['maxHP'] = Formulas::getMaxHP($character);
                    break;
                case $characteristicId == Constants::INTELLECT:
                    $character['intellect']++;
                    break;
                case $characteristicId == Constants::WISDOM:
                    $character['wisdom']++;
                    break;
                case $characteristicId == Constants::RESISTANCE:
                    $character['response']++;
                    break;
            }

            $character['trainings_count']--;

            /**/
            dispatch(new SaveCharacterJob($character));
            /**/
            return "Вы потренировали {$characteristicFullName} за одну тренировку";
        } elseif ($character['trainings_count'] == 0) {
            return "Вам нужны тренировки, чтобы тренировать {$characteristicFullName}";
        }

    }


    public function takeOffItem(&$character, $itemName)
    {
        foreach ($character['stuff'] as $key => $stuff) {

            /**/
//            Debugger::PrintToFile('--takeOffItem', mb_stripos($itemName, $stuff['name']));
            /**/

            if ($stuff['pivot']['slot_id'] != Slot::IN_INVENTORY && (mb_stripos($stuff['name'], $itemName) !== false)) {
                if (count($this->getInventoryItems($character)) < 3) {
//                    $stuff['slot_id'] = Slot::IN_INVENTORY;
                    $character['stuff'][$key]['pivot']['slot_id'] = Slot::IN_INVENTORY;
                    $this->setDamageRange($character);
                    return "Вы прекратили использовать " . mb_strtolower($stuff['name']) . ".";
                } else {
                    return "Инвентарь полон.";
                }
            }
        }
        return "Вы не используете '" . mb_strtolower($itemName) . "'.";
    }


    public function armWithItem(&$character, $item)
    {

        /**/
//            Debugger::PrintToFile('--takeOffItem', mb_stripos($itemName, $stuff['name']));
        /**/

        $inventoryItems = $this->getInventoryItems($character);
        foreach ($inventoryItems as $key => $inventoryItem) {
            if (mb_stripos($inventoryItem['name'], $item) !== false) {
                $stuffKey = $key;
                $stuff = $inventoryItem;
                break;
            }
        }
        if (empty($stuff)) {
            return "У вас нет '" . $item . "'.";
        }

        //Получить массив занятых слотов
        $occupiedSlotsIds = array_column(array_column($character['stuff'], 'pivot'), 'slot_id');

        if (!in_array($stuff['dest_slot_id'], $occupiedSlotsIds)) {
            $stuff['pivot']['character_id'] = $character['id'];
            $stuff['pivot']['stuff_id'] = $stuff['id'];
            $stuff['pivot']['slot_id'] = $stuff['dest_slot_id'];
            $character['stuff'][$stuffKey] = $stuff;
            //todo заменить на const
            if ($stuff['stuff_type_id'] == 1) {
                $this->setDamageRange($character);
            }
            return $stuff['name'] . " теперь у вас " . $stuff['dest_slot']['name'];

            //todo заменить на const
        } elseif ($stuff['stuff_type_id'] == 1) {
            return "Вы уже вооружены чем-то.";
        } else {
            return "У вас уже что-то " . $stuff['dest_slot']['name'];
        }
    }

    public function learnSkill(&$character, $argument)
    {
        $skillsAvailableToLearn = Profession::with('available_skills')->where('id', $character['profession_id'])->get()->toArray()[0]['available_skills'];

        /**/
        Debugger::PrintToFile('--$skillsAvailableToLearn', $skillsAvailableToLearn);
        /**/

        /**/
        Debugger::PrintToFile('--skillsAvailableToLearn-argument', $argument);
        /**/

        if (empty($argument) || $argument == 'все') {
            $skillsAvailableToLearnMessage = "Вы можете учить:";
            foreach ($skillsAvailableToLearn as $skillAvailableToLearn) {
                if ($skillAvailableToLearn['pivot']['learning_level'] <= $character['level']) {
                    $skillsAvailableToLearnMessage .= '<br>' . $skillAvailableToLearn['name'];
                }
            }
        }

        return $skillsAvailableToLearnMessage;

    }

}
