<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    const START_ROOM_UUID = 'e9ddeb92-1161-408c-b613-4ce779ca8469';

    public $timestamps = false;

    protected $casts = [
        'exits' => 'json',
    ];
}
