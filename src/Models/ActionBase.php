<?php

namespace Aviator\Helpdesk\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ActionBase
 * @package Aviator\Helpdesk\Models
 * @property \Aviator\Helpdesk\Models\Action action
 */
class ActionBase extends Model
{
    use SoftDeletes;

    /**
     * Fields to be mutated to dates.
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Fields exempt from mass assignment.
     * @var array
     */
    protected $guarded = [];

    /**
     * Fields to cast as types.
     * @var array
     */
    protected $casts = [
        'is_visible' => 'boolean',
    ];

    /**
     * Owned by ticket.
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Owned by an agent.
     */
    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    /**
     * Owned by an user.
     */
    public function user()
    {
        return $this->belongsTo(config('helpdesk.userModel'));
    }

    /**
     * Associated with one action.
     */
    public function action()
    {
        return $this->morphOne(Action::class, 'object');
    }
}
