<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Slot extends Model
{
    const IN_RIGHT_HAND = 1;
    const IN_LEFT_HAND = 2;
    const IN_BOTH_HANDS = 3;
    const IN_INVENTORY = 4;

    public $timestamps = false;
}
