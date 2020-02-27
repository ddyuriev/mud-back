<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Character extends Model
{

    const LEVEL_1_WARRIOR = 0;

    public function user()
    {
//        return $this->hasOne('App\User');
        return $this->hasOne('App\User', 'id', 'user_id');
    }
}
