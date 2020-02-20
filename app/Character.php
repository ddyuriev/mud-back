<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Character extends Model
{

    public function user()
    {
//        return $this->hasOne('App\User');

        return $this->hasOne('App\User', 'id', 'user_id');
    }
}
