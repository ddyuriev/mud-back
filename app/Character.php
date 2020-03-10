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

    public function profession()
    {
        return $this->hasOne('App\Profession', 'id', 'profession_id');
    }

    public function skills()
    {
        return $this->belongsToMany('App\Skill')->withPivot('value');
    }


//    public function getNameAttribute($value)
//    {
//        return ucfirst($value);
//        return unserialize($value);
////        return 1;
//    }
}
