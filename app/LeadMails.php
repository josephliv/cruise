<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeadMails extends Model
{
    public function agent(){
        return $this->hasOne('App\User', 'id', 'agent_id');
    }

    public function old_agent(){
        return $this->hasOne('App\User', 'id', 'old_agent_id');
    }
}
