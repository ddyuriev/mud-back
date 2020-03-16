<?php

namespace App;

use App\Helpers\Debugger;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Skill
 * @package App
 */
class Skill extends Model
{
    const LONG_SWORDS_ID = 101;
    const BASTARD_SWORDS_ID = 102;
    const TWO_HANDED_SWORDS_ID = 103;
    const SHORT_BLADES_ID = 104;

    const KNOCK_DOWN_ID = 201;


//    public function characters()
//    {
//        return $this->belongsToMany('App\Character')->withPivot('value');
//    }

    public function professions()
    {

        /**/
//        Debugger::PrintToFile('$this', $this);
        /**/

//        dd($this->first());


        return $this->belongsToMany('App\Profession')->withPivot('learning_level')/*->where('profession_id', 1)*/
            ;
    }

    public function learning_level_check()
    {
        /**/
//        Debugger::PrintToFile('learning_level_check' . time(), '');
        /**/

        return $this->hasMany('App\ProfessionSkill', 'skill_id', 'id')
            ->select(['skill_id', 'learning_level']);
    }

    /**/

    public function characterSkill()
    {
        return $this->hasOne('App\CharacterSkill');
    }

    /**/
}
