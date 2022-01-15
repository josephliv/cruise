<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed                            email_from
 * @property mixed|string                     email_imap_id
 * @property int|mixed                        agent_id
 * @property mixed|string                     subject
 * @property false|mixed|string               body
 * @property array|mixed|string|string[]|null attachment
 * @property Carbon|mixed                     received_date
 * @property int|mixed                        priority
 * @property mixed                            to_group
 */
class LeadMails extends Model
{

    public function agent(){
        return $this->hasOne('App\User', 'id', 'agent_id');
    }

    public function old_agent(){
        return $this->hasOne('App\User', 'id', 'old_agent_id');
    }
}
