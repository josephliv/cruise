<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Group
 *
 * @property int $id
 * @property string $name
 * @property int $default_priority
 * @property int|null $order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Group newModelQuery()
 * @method static Builder|Group newQuery()
 * @method static Builder|Group query()
 * @method static Builder|Group whereCreatedAt($value)
 * @method static Builder|Group whereDefaultPriority($value)
 * @method static Builder|Group whereId($value)
 * @method static Builder|Group whereName($value)
 * @method static Builder|Group whereOrder($value)
 * @method static Builder|Group whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Group extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'default_priority',
    ];

    /**
     * A way to get a Users Group name although this is another Fetch
     * ** Tested TB 27th Jan 2022 **
     * @return Model|\Illuminate\Database\Query\Builder|object|null
     */
    public function group_name()
    {
        $row = \App\Group::select('groups.*')
            ->join('users', 'users.user_group', '=', 'groups.id')
            ->first();
        return $row;
    }
}
