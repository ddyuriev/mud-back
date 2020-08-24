<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Slot extends Model
{
    const IN_RIGHT_HAND = 1;
    const IN_LEFT_HAND = 2;
    const IN_BOTH_HANDS = 3;

    const RIGHT_WRIST = 10;

    const IN_INVENTORY = 100;

    public $timestamps = false;

    /*todo test*/
//    public function stuff()
//    {
//        return $this->hasMany('App\Stuff', 'dest_slot_id', 'id');
//    }

    /**/
}
