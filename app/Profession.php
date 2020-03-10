<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Profession extends Model
{
    const WARRIOR_ID = 1;
    const WIZARD_ID = 2;
    const CLERIC_ID = 3;

    public $timestamps = false;
}
