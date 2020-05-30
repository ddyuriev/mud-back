<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stuff extends Model
{
    protected $table = 'stuff';
    
    public $timestamps = false;

    public function dest_slot()
    {
//        return $this->belongsTo('App\Slot');
//        return $this->belongsTo('App\Slot', 'id', 'dest_slot_id');
        return $this->belongsTo('App\Slot', 'dest_slot_id', 'id');
    }

}
