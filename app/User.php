<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * App\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property int|null $is_admin
 * @property int|null $leads_allowed
 * @property string|null $time_set_init
 * @property string|null $time_set_final
 * @property int|null $user_group
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User|null $agent
 * @property-read DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User query()
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereEmailVerifiedAt($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereIsAdmin($value)
 * @method static Builder|User whereLeadsAllowed($value)
 * @method static Builder|User whereName($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User whereRememberToken($value)
 * @method static Builder|User whereTimeSetFinal($value)
 * @method static Builder|User whereTimeSetInit($value)
 * @method static Builder|User whereUpdatedAt($value)
 * @method static Builder|User whereUserGroup($value)
 * @mixin Eloquent
 */
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

    public function agent()
    {
        return $this->hasOne('App\User', 'id', 'agent_id');
    }

    /**
     * Only works if the field name matches the primary key name i.e. group_id and not user_group (Bad Name)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group()
    {
        return $this->belongsTo('App\Group');
    }

    /**
     * Get the users table (for Manage Agents ) and group_name for each user
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function get_user()
    {
        $rows = DB::table('users')
            ->select('users.id', 'users.name', 'users.email', 'users.created_at', 'groups.name as group_name','groups.id as group_id')
            ->join('groups', 'groups.id', '=', 'users.user_group')
            ->paginate(15);

        return $rows;
    }
}
