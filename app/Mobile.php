<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mobile extends Model
{
    public $timestamps = false;

    protected $casts = [
        'pseudonyms' => 'array',
    ];

    public function profession()
    {
        return $this->hasOne('App\Profession', 'id', 'profession_id');
    }
}
