<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Priority
 *
 * @property int             $id
 * @property string          $description
 * @property int             $field
 * @property string          $condition
 * @property string|null     $send_to_email
 * @property int|null        $user_group
 * @property int|null        $priority
 * @property Carbon|null     $created_at
 * @property Carbon|null     $updated_at
 * @property-read Group|null $group
 * @method static Builder|Priority newModelQuery()
 * @method static Builder|Priority newQuery()
 * @method static Builder|Priority query()
 * @method static Builder|Priority whereCondition($value)
 * @method static Builder|Priority whereCreatedAt($value)
 * @method static Builder|Priority whereDescription($value)
 * @method static Builder|Priority whereField($value)
 * @method static Builder|Priority whereId($value)
 * @method static Builder|Priority wherePriority($value)
 * @method static Builder|Priority whereSendToEmail($value)
 * @method static Builder|Priority whereUpdatedAt($value)
 * @method static Builder|Priority whereUserGroup($value)
 * @mixin Eloquent
 */
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
