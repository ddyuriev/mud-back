<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stuff extends Model
{
    protected $table = 'stuff';
    
    public $timestamps = false;

    public function slot()
    {
        return $this->belongsTo('App\Slot');
    }

}
