<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * App\LeadMails
 *
 * @property mixed          to_group
 * @property int            $id
 * @property string|null    $email_imap_id
 * @property string         $email_from
 * @property int            $agent_id
 * @property int|null       $old_agent_id
 * @property string         $subject
 * @property string|null    $body
 * @property string|null    $attachment
 * @property string         $received_date
 * @property int|null       $priority
 * @property int|null       $rejected
 * @property string|null    $rejected_message
 * @property string|null    $assigned_date
 * @property string|null    $old_assigned_date
 * @property string|null    $reassigned_message
 * @property int|null       $to_veteran
 * @property string|null    $sent_date
 * @property Carbon|null    $created_at
 * @property Carbon|null    $updated_at
 * @property-read User|null $agent
 * @property-read User|null $old_agent
 * @method static Builder|LeadMails newModelQuery()
 * @method static Builder|LeadMails newQuery()
 * @method static Builder|LeadMails query()
 * @method static Builder|LeadMails whereAgentId($value)
 * @method static Builder|LeadMails whereAssignedDate($value)
 * @method static Builder|LeadMails whereAttachment($value)
 * @method static Builder|LeadMails whereBody($value)
 * @method static Builder|LeadMails whereCreatedAt($value)
 * @method static Builder|LeadMails whereEmailFrom($value)
 * @method static Builder|LeadMails whereEmailImapId($value)
 * @method static Builder|LeadMails whereId($value)
 * @method static Builder|LeadMails whereOldAgentId($value)
 * @method static Builder|LeadMails whereOldAssignedDate($value)
 * @method static Builder|LeadMails wherePriority($value)
 * @method static Builder|LeadMails whereReassignedMessage($value)
 * @method static Builder|LeadMails whereReceivedDate($value)
 * @method static Builder|LeadMails whereRejected($value)
 * @method static Builder|LeadMails whereRejectedMessage($value)
 * @method static Builder|LeadMails whereSentDate($value)
 * @method static Builder|LeadMails whereSubject($value)
 * @method static Builder|LeadMails whereToGroup($value)
 * @method static Builder|LeadMails whereToVeteran($value)
 * @method static Builder|LeadMails whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class LeadMails extends Model {

    /**
     * @return HasOne
     */
    public function agent()
    {
        return $this->hasOne('App\User', 'id', 'agent_id');
    }

    /**
     * @return HasOne
     * @todo - minor - PHPStorm not seeing it being used
     */
    public function group_name()
    {
        return $this->hasOne('App\Group', 'id', 'to_group');
    }

    /**
     * @return HasOne
     * @todo - minor - PHPStorm not seeing it being used
     */
    public function old_agent()
    {
        return $this->hasOne('App\User', 'id', 'old_agent_id');
    }
}
