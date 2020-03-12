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
        return $this->belongsToMany('App\Skill')
            ->withPivot('value')
            ;
    }

//    public function skills2()
//    {
////        return $this->hasManyThrough('App\Profession', 'App\Skill');
////        return $this->hasManyThrough('App\Profession', 'App\CharacterSkill');
////        return $this->hasManyThrough('App\Skill', 'App\Profession');
//
////        return $this->hasOneThrough('App\Profession',
////            'App\CharacterSkill',
////            'App\CharacterSkill',
////            'App\CharacterSkill',
////            'App\CharacterSkill',
////            'App\CharacterSkill'
////        );
//
//        return $this->hasManyThrough(
//            'App\ProfessionSkill',
//            'App\CharacterSkill',
//            'skill_id',
//            'skill_id',
//            'profession_id',
//            'character_id'
//        );
//    }

//    public function profession_skills()
//    {
//        return $this->hasMany('App\ProfessionSkill', 'profession_id', 'profession_id');
//    }

    public function skills3()
    {
        return $this->belongsToMany('App\Skill')
            ->withPivot('value')
//            ->join('items', 'bundle.child_item_id','=', 'items.item_id')
            ->join('profession_skill', 'skills.id','=', 'profession_skill.skill_id')
            ;
    }


//    public function getNameAttribute($value)
//    {
//        return ucfirst($value);
//        return unserialize($value);
////        return 1;
//    }
}
