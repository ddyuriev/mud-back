<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Profession extends Model
{
    const WARRIOR_ID = 1;
    const WIZARD_ID = 2;
    const CLERIC_ID = 3;

    public $timestamps = false;

    public function professionSkills()
    {
//        return $this->belongsToMany('App\Skill')->withPivot('learning_level');
        return $this->belongsToMany('App\Skill')->withPivot('learning_level')
//            ->join('character_skill', 'character_skill.skill_id', '=', 'profession_skill.skill_id')
            ->with('characterSkill')
            ;
    }
}
