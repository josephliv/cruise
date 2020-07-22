<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Priority extends Model
{
    //
    protected $fillable = [
        'description', 'field', 'condition', 'send_to_email', 'user_group', 'priority',
    ];

    public function group(){
        return $this->hasOne('App\Group', 'id', 'user_group');
    }   
}
