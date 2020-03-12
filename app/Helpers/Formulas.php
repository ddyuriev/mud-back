<?php

namespace App\Helpers;

/**
 * Class Debugger
 *
 * @package App\Helpers
 */
class Formulas
{

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
}
