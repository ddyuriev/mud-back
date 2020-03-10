<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
//    const START_ROOM_UUID = 'e9ddeb92-1161-408c-b613-4ce779ca8469';
    const START_ROOM_INNER_ID = '1_1';

    public $timestamps = false;

    protected $casts = [
        'exits' => 'json',
        'mobiles' => 'json',
    ];

    public function mobiles()
    {
        return $this->hasMany('App\Mobile', 'room_id', 'id')->orderBy('id', 'desc');
    }
}


