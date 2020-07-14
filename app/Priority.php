<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Priority extends Model
{
    //
    protected $fillable = [
        'description', 'field', 'condition', 'send_to_email', 'send_to_veteran', 'priority',
    ];
}
