<?php

namespace App\Helpers;

use App\Profession;

/**
 * Class Debugger
 *
 * @package App\Helpers
 */
class Formulas
{
    CONST WARRIOR_LEVEL_1_EXP = 0;
    CONST WARRIOR_LEVEL_2_EXP = 2000;
    CONST WARRIOR_LEVEL_3_EXP = 4000;
    CONST WARRIOR_LEVEL_4_EXP = 8000;
    CONST WARRIOR_LEVEL_5_EXP = 16000;
    CONST WARRIOR_LEVEL_6_EXP = 32000;
    CONST WARRIOR_LEVEL_7_EXP = 64000;
    CONST WARRIOR_LEVEL_8_EXP = 140000;
    CONST WARRIOR_LEVEL_9_EXP = 220000;
    CONST WARRIOR_LEVEL_10_EXP = 320000;
    CONST WARRIOR_LEVEL_11_EXP = 440000;
    CONST WARRIOR_LEVEL_12_EXP = 580000;
    CONST WARRIOR_LEVEL_13_EXP = 740000;
    CONST WARRIOR_LEVEL_14_EXP = 950000;
    CONST WARRIOR_LEVEL_15_EXP = 1150000;
    CONST WARRIOR_LEVEL_16_EXP = 1400000;
    CONST WARRIOR_LEVEL_17_EXP = 1700000;
    CONST WARRIOR_LEVEL_18_EXP = 2050000;
    CONST WARRIOR_LEVEL_19_EXP = 2450000;
    CONST WARRIOR_LEVEL_20_EXP = 2950000;
    CONST WARRIOR_LEVEL_21_EXP = 3950000;
    CONST WARRIOR_LEVEL_22_EXP = 5250000;
    CONST WARRIOR_LEVEL_23_EXP = 6750000;
    CONST WARRIOR_LEVEL_24_EXP = 8450000;
    CONST WARRIOR_LEVEL_25_EXP = 10350000;
    CONST WARRIOR_LEVEL_26_EXP = 14000000;
    CONST WARRIOR_LEVEL_27_EXP = 21000000;
    CONST WARRIOR_LEVEL_28_EXP = 31000000;
    CONST WARRIOR_LEVEL_29_EXP = 44000000;
    CONST WARRIOR_LEVEL_30_EXP = 60000000;
    CONST WARRIOR_LEVEL_31_EXP = 79000000;


    public static function strengthDamageBonus($strength)
    {

//  4  -4
//  5  -3
//  6  -2
//  7  -1
//  8  -1
//  9  -1
// 10  0
// 11  0
// 12  0
// 13  0
// 14  0
// 15  0
// 16  1
// 17  1
// 18  1
// 19  2
// 20  2
// 21  3
// 22  3
// 23  3
// 24  4
// 25  4
// 26  5
// 27  5
// 28  6

        $damageBonus = 0;

        switch ($strength) {
            case 4:
                $damageBonus = -4;
                break;
            case 5:
                $damageBonus = -3;
                break;
            case 6:
                $damageBonus = -2;
                break;
            case 7:
            case 8:
            case 9:
                $damageBonus = -1;
                break;
            case 10:
            case 11:
            case 12:
            case 13:
            case 14:
            case 15:
                $damageBonus = 0;
                break;
            case 16:
            case 17:
            case 18:
                $damageBonus = 1;
                break;
            case 19:
            case 20:
                $damageBonus = 2;
                break;
            case 21:
            case 22:
            case 23:
                $damageBonus = 3;
                break;
            case 24:
            case 25:
                $damageBonus = 4;
                break;
            case 26:
            case 27:
                $damageBonus = 5;
                break;
            case 28:
                $damageBonus = 6;
                break;
        }

        return $damageBonus;
    }

    public static function damageMessage($damage)
    {
        switch ($damage) {
            case $damage >= 1 && $damage <= 2:
                $damageMessage = 'легонько';
                break;
            case $damage >= 3 && $damage <= 4:
                $damageMessage = 'слегка';
                break;
            case $damage >= 5 && $damage <= 6:
                $damageMessage = 'легко';
                break;
            case $damage >= 7 && $damage <= 10:
                $damageMessage = 'сильно';
                break;
            case $damage >= 11 && $damage <= 14:
                $damageMessage = 'очень сильно';
                break;
            case $damage >= 15 && $damage <= 19:
                $damageMessage = 'чрезвычайно сильно';
                break;
            case $damage >= 20 && $damage <= 23:
                $damageMessage = 'смертельно';
                break;
            case $damage >= 24 && $damage <= 35:
                $damageMessage = 'БОЛЬНО';
                break;
            case $damage >= 36 && $damage <= 50:
                $damageMessage = 'ОЧЕНЬ БОЛЬНО';
                break;
            case $damage >= 51:
                $damageMessage = 'НЕВЫНОСИМО БОЛЬНО';
                break;
        }

        return $damageMessage;
    }

    public static function calculateLevel($professionId, int $exp)
    {

//        [ 6]    32000-63999
//[ 7]    64000-139999
//[ 8]   140000-219999
//[ 9]   220000-319999
//[10]   320000-439999
//[11]   440000-579999
//[12]   580000-739999
//[13]   740000-949999
//[14]   950000-1149999
//[15]  1150000-1399999
//[16]  1400000-1699999
//[17]  1700000-2049999
//[18]  2050000-2449999
//[19]  2450000-2949999
//[20]  2950000-3949999
//[21]  3950000-5249999
//[22]  5250000-6749999
//[23]  6750000-8449999
//[24]  8450000-10349999
//[25] 10350000-13999999
//[26] 14000000-20999999
//[27] 21000000-30999999
//[28] 31000000-43999999
//[29] 44000000-59999999
//[30] 60000000-78999999

        /**/
//        Debugger::PrintToFile('--------------++++$exp', $exp);
        /**/

        switch ($professionId) {
            case Profession::WARRIOR_ID:
                switch (true) {
                    case $exp >= 0 && $exp < self::WARRIOR_LEVEL_2_EXP:
                        $level = 1;
                        break;
                    case $exp >= self::WARRIOR_LEVEL_2_EXP && $exp < self::WARRIOR_LEVEL_3_EXP:
                        $level = 2;
                        break;
                    case $exp >= self::WARRIOR_LEVEL_3_EXP && $exp < self::WARRIOR_LEVEL_4_EXP:
                        $level = 3;
                        break;
                    case $exp >= self::WARRIOR_LEVEL_4_EXP && $exp < self::WARRIOR_LEVEL_5_EXP:
                        $level = 4;
                        break;
                    case $exp >= self::WARRIOR_LEVEL_5_EXP && $exp < self::WARRIOR_LEVEL_6_EXP:
                        $level = 5;
                        break;
                    case $exp >= self::WARRIOR_LEVEL_6_EXP && $exp < self::WARRIOR_LEVEL_7_EXP:
                        $level = 6;
                        break;
                    case $exp >= self::WARRIOR_LEVEL_7_EXP && $exp < self::WARRIOR_LEVEL_8_EXP:
                        $level = 7;
                        break;
                    case $exp >= self::WARRIOR_LEVEL_8_EXP && $exp < self::WARRIOR_LEVEL_9_EXP:
                        $level = 8;
                        break;
                    case $exp >= self::WARRIOR_LEVEL_9_EXP && $exp < self::WARRIOR_LEVEL_10_EXP:
                        $level = 9;
                        break;
                    case $exp >= self::WARRIOR_LEVEL_10_EXP && $exp < self::WARRIOR_LEVEL_11_EXP:
                        $level = 10;
                        break;
                    case $exp >= self::WARRIOR_LEVEL_11_EXP && $exp < self::WARRIOR_LEVEL_12_EXP:
                        $level = 11;
                        break;
                    case $exp >= self::WARRIOR_LEVEL_12_EXP && $exp < self::WARRIOR_LEVEL_13_EXP:
                        $level = 12;
                        break;
                    case $exp >= self::WARRIOR_LEVEL_13_EXP && $exp < self::WARRIOR_LEVEL_14_EXP:
                        $level = 13;
                        break;
                    case $exp >= self::WARRIOR_LEVEL_14_EXP && $exp < self::WARRIOR_LEVEL_15_EXP:
                        $level = 14;
                        break;
                    case $exp >= self::WARRIOR_LEVEL_15_EXP && $exp < self::WARRIOR_LEVEL_16_EXP:
                        $level = 15;
                        break;
                    case $exp >= self::WARRIOR_LEVEL_16_EXP && $exp < self::WARRIOR_LEVEL_17_EXP:
                        $level = 16;
                        break;
                    case $exp >= self::WARRIOR_LEVEL_17_EXP && $exp < self::WARRIOR_LEVEL_18_EXP:
                        $level = 17;
                        break;
                    case $exp >= self::WARRIOR_LEVEL_18_EXP && $exp < self::WARRIOR_LEVEL_19_EXP:
                        $level = 18;
                        break;
                    case $exp >= self::WARRIOR_LEVEL_19_EXP && $exp < self::WARRIOR_LEVEL_20_EXP:
                        $level = 19;
                        break;
                    case $exp >= self::WARRIOR_LEVEL_20_EXP && $exp < self::WARRIOR_LEVEL_21_EXP:
                        $level = 20;
                        break;
                    case $exp >= self::WARRIOR_LEVEL_21_EXP && $exp < self::WARRIOR_LEVEL_22_EXP:
                        $level = 21;
                        break;
                    case $exp >= self::WARRIOR_LEVEL_22_EXP && $exp < self::WARRIOR_LEVEL_23_EXP:
                        $level = 22;
                        break;
                    case $exp >= self::WARRIOR_LEVEL_23_EXP && $exp < self::WARRIOR_LEVEL_24_EXP:
                        $level = 23;
                        break;
                    case $exp >= self::WARRIOR_LEVEL_24_EXP && $exp < self::WARRIOR_LEVEL_25_EXP:
                        $level = 24;
                        break;
                    case $exp >= self::WARRIOR_LEVEL_25_EXP && $exp < self::WARRIOR_LEVEL_26_EXP:
                        $level = 25;
                        break;
                    case $exp >= self::WARRIOR_LEVEL_26_EXP && $exp < self::WARRIOR_LEVEL_27_EXP:
                        $level = 26;
                        break;
                    case $exp >= self::WARRIOR_LEVEL_27_EXP && $exp < self::WARRIOR_LEVEL_28_EXP:
                        $level = 27;
                        break;
                    case $exp >= self::WARRIOR_LEVEL_28_EXP && $exp < self::WARRIOR_LEVEL_29_EXP:
                        $level = 28;
                        break;
                    case $exp >= self::WARRIOR_LEVEL_29_EXP && $exp < self::WARRIOR_LEVEL_30_EXP:
                        $level = 29;
                        break;
                    case $exp >= self::WARRIOR_LEVEL_30_EXP && $exp < self::WARRIOR_LEVEL_31_EXP:
                        $level = 30;
                        break;
                    default:
                        $level = 30;
                        break;
                }
                break;
        }

        return $level;
    }

    public static function toNextLevel($professionId, $exp, $level)
    {
        $nextLevel = ++$level;

        switch ($professionId) {
            case Profession::WARRIOR_ID:
                $nextLevelExp = constant("SELF::WARRIOR_LEVEL_{$nextLevel}_EXP");
                break;
        }
        return $nextLevelExp - $exp;
    }
}
