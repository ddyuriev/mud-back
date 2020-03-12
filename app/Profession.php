<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Profession extends Model
{
    const WARRIOR_ID = 1;
    const WIZARD_ID = 2;
    const CLERIC_ID = 3;

    public $timestamps = false;

    public function skills()
    {
        return $this->belongsToMany('App\Skill')->withPivot('learning_level');
    }
}
