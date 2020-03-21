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

    const STATE_MENU = 1;
    const STATE_IN_GAME = 2;
    const STATE_IN_BATTLE = 3;

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
