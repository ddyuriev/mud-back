<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProfessionSkill extends Model
{
    protected $table = 'profession_skill';

    public $incrementing = false;
    protected $primaryKey = null;

    public $timestamps = false;

    protected $fillable = ['learning_level'];

}
