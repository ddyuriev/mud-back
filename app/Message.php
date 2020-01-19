<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = 'message';

    public $timestamps = false;


    public function user()
    {
        return $this->hasOne('App\User', 'uniqueId', 'uniqueId');
    }

}
