<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    const LONG_SWORDS_ID = 101;
    const BASTARD_SWORDS_ID = 102;
    const TWO_HANDED_SWORDS_ID = 103;
    const SHORT_BLADES_ID = 104;

    const KNOCK_DOWN_ID = 201;

    protected $casts = [
        'learning_level' => 'json',
    ];

    public function characters()
    {
        return $this->belongsToMany('App\Character')->withPivot('value');
    }
}
