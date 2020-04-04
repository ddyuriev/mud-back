<?php

namespace App\Helpers;

/**
 * Class Constants
 *
 * @package App\Helpers
 */
class Constants
{
    const COLOR_SCHEME_BLACK = 1;
    const COLOR_SCHEME_WHITE = 2;

    const STATE_MENU = 100;
    const STATE_IN_GAME = 200;
    const STATE_IN_BATTLE = 300;
    const ENTER_NEW_CHARACTER_NAME = 400;
    const ENTER_NEW_CHARACTER_PROFESSION = 402;

    const USER_INPUT_CREATE_NEW_CHARACTER = 7;

    const USER_INPUT_CREATE_PROFESSION_WARRIOR = 1;


    const STRENGTH = 1;
    const DEXTERITY = 2;
    const CONSTITUTION = 3;
    const INTELLECT = 4;
    const WISDOM = 5;
    const RESISTANCE = 6;






    /**
     * @param $schemeId
     * @param $colorLevel
     * @return string
     */
    public static function getConditionEstimateCssClass($schemeId, $colorLevel)
    {
        $class = "";
        switch (true) {
            case $schemeId == self::COLOR_SCHEME_BLACK && $colorLevel == 0:
                $class = "contrast-color";
                break;
            case $schemeId == self::COLOR_SCHEME_BLACK && $colorLevel == 1:
                $class = "health-good";
                break;
            case $schemeId == self::COLOR_SCHEME_BLACK && $colorLevel == 2:
                $class = "actor-attack";
                break;
            case $schemeId == self::COLOR_SCHEME_BLACK && $colorLevel == 3:
                $class = "health-bad";
                break;
        }
        return $class;
    }

    public static function getCharacteristicNameByConstant($constantValue){

        $characteristic = "";
        switch ($constantValue) {
            case self::STRENGTH:
                $characteristic = "strength";
                break;
            case self::DEXTERITY:
                $characteristic = "dexterity";
                break;
            case self::CONSTITUTION:
                $characteristic = "constitution";
                break;
            case self::INTELLECT:
                $characteristic = "intellect";
                break;
            case self::WISDOM:
                $characteristic = "wisdom";
                break;
            case self::RESISTANCE:
                $characteristic = "resistance";
                break;
        }
        return $characteristic;
    }
}
