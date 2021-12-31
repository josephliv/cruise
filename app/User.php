<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'leads_allowed', 'time_set_init', 'time_set_final', 'user_group',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     *
     * @param int $id
     * @todo user::delete()
     */
    public function delete() {

    }

    /**
     * @return HasOne
     */
//    public function create($params) {
//        return $this;
//    }

    public function agent() {
        return $this->hasOne('App\User', 'id', 'agent_id');
    }
}
