<?php

namespace Aviator\Helpdesk\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property mixed user
 * @property int id
 * @property bool is_super
 * @property \Illuminate\Support\Collection teams
 * @property \Carbon\Carbon created_at
 * @property \Aviator\Helpdesk\Models\Team team
 * @property \Illuminate\Support\Collection teamLeads
 * @method static Builder withTrashed()
 */
class Agent extends Model
{
    use SoftDeletes, Notifiable;

    /** @var \Illuminate\Database\Eloquent\Model */
    protected $userModelName;

    /** @var array */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /** @var array */
    protected $guarded = [];

    /** @var array */
    protected $casts = [
        'is_team_lead' => 'boolean',
        'is_super' => 'boolean',
    ];

    /**
     * Set the table name from the Helpdesk config.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('helpdesk.tables.agents'));
        $this->userModelName = config('helpdesk.userModel');
    }

    /*
     * Public Api
     */

    /**
     * Route notifications for the mail channel.
     * @return string
     */
    public function routeNotificationForMail()
    {
        $email = config('helpdesk.userModelEmailColumn');

        return $this->user->$email;
    }

    /**
     * Make the Agent a team lead.
     * @param \Aviator\Helpdesk\Models\Team $team
     * @return $this
     */
    public function makeTeamLeadOf(Team $team)
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
     * Make the Agent a team lead.
     * @param \Aviator\Helpdesk\Models\Team $team
     * @return $this
     */
    public function removeTeamLeadOf(Team $team)
    {
        $this->teams()->detach($team);

        $this->teams()->attach($team, [
            'is_team_lead' => 0,
        ]);

        return $this;
    }

    /**
     * Add the agent to a team.
     * @param Team $team
     * @return $this
     */
    public function addToTeam(Team $team)
    {
        $this->teams()->attach($team->id);

        return $this;
    }

    /**
     * Remove the agent from a team.
     * @param  Team   $team
     * @return $this
     */
    public function removeFromTeam(Team $team)
    {
        $this->teams()->detach($team->id);

        return $this;
    }

    /**
     * Add the agent to multiple teams.
     * @param array $teams
     * @return $this
     */
    public function addToTeams(array $teams)
    {
        foreach ($teams as $team) {
            $this->teams()->attach($team);
        }

        return $this;
    }

    /**
     * Remove the agent from multiple teams.
     * @param  array $teams
     * @return $this
     */
    public function removeFromTeams(array $teams)
    {
        foreach ($teams as $team) {
            $this->teams()->detach($team);
        }

        return $this;
    }

    /*
     * Booleans
     */

    /**
     * Is this agent a member of this team.
     * @param  Team    $team
     * @return bool
     */
    public function isMemberOf(Team $team)
    {
        return $team->agents->pluck('id')->contains($this->id);
    }

    /**
     * Check if the user is a supervisor.
     * @return bool
     */
    public function isSuper ()
    {
        return (bool) $this->is_super;
    }

    /*
     * Relationships
     */

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo */
    public function user()
    {
        return $this->belongsTo($this->userModelName);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsToMany */
    public function teams()
    {
        return $this->belongsToMany(Team::class, config('helpdesk.tables.agent_team'))
            ->withPivot('is_team_lead')
            ->withTimestamps();
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsToMany */
    public function teamLeads()
    {
        return $this->belongsToMany(Team::class, config('helpdesk.tables.agent_team'))
            ->withPivot('is_team_lead')
            ->withTimestamps()
            ->wherePivot('is_team_lead', 1);
    }
}
