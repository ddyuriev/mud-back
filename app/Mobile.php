<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mobile extends Model
{
    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $casts = [
        'pseudonyms' => 'array',
    ];

    /**/
    protected $fillable = ['mobile_inner_id'];
    /**/

    public function profession()
    {
        return $this->hasOne('App\Profession', 'id', 'profession_id');
    }
}
