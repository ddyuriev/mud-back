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



    /**
     * @param $schemeId
     * @param $colorLevel
     * @return string
     */
    public static function getConditionEstimateCssClass($schemeId, $colorLevel)
    {
        $class = '';
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
}
