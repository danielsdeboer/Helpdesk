<?php

namespace Aviator\Helpdesk\Models;

use Aviator\Helpdesk\Models\Pool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Agent extends Model
{
    use SoftDeletes, Notifiable;

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $guarded = [];

    protected $casts = [
        'is_team_lead' => 'boolean',
    ];

    /**
     * Set the table name from the Helpdesk config
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('helpdesk.tables.agents'));
    }

    /**
     * Route notifications for the mail channel.
     *
     * @return string
     */
    public function routeNotificationForMail()
    {
        $email = config('helpdesk.userModelEmailColumn');

        return $this->user->$email;
    }

    /**
     * Make the Agent a team lead
     * @return $this
     */
    public function makeTeamLeadOf(Pool $team)
    {
        // If the agent is already in the team but not team lead
        // we need to detach first. This does nothing otherwise.
        $this->teams()->detach($team->id);

        $this->teams()->attach($team->id, [
            'is_team_lead' => 1,
        ]);

        return $this;
    }

    /**
     * Make the Agent a team lead
     * @return $this
     */
    public function removeTeamLeadOf(Pool $team)
    {
        $this->teams()->detach($team);

        $this->teams()->attach($team, [
            'is_team_lead' => 0,
        ]);

        return $this;
    }

    public function user() {
        return $this->belongsTo(config('helpdesk.userModel'));
    }

    public function teams() {
        return $this->belongsToMany(Pool::class)->withPivot('is_team_lead')->withTimestamps();;
    }
}
