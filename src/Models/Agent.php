<?php

namespace Aviator\Helpdesk\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

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
     * Set the table name from the Helpdesk config.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('helpdesk.tables.agents'));
    }

    ////////////////
    // Public API //
    ////////////////

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
     * Make the Agent a team lead.
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
     * Make the Agent a team lead.
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

    /**
     * Add the agent to a team.
     * @param Pool $team
     * @return $this
     */
    public function addToTeam(Pool $team)
    {
        $this->teams()->attach($team->id);

        return $this;
    }

    /**
     * Remove the agent from a team.
     * @param  Pool   $team
     * @return $this
     */
    public function removeFromTeam(Pool $team)
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

    /**
     * Is this agent a member of this pool.
     * @param  Pool    $team
     * @return bool
     */
    public function isMemberOf(Pool $team)
    {
        return $team->agents->pluck('id')->contains($this->id);
    }

    ///////////////////
    // Relationships //
    ///////////////////

    public function user()
    {
        return $this->belongsTo(config('helpdesk.userModel'));
    }

    public function teams()
    {
        return $this->belongsToMany(Pool::class, config('helpdesk.tables.agent_pool'))
            ->withPivot('is_team_lead')
            ->withTimestamps();
    }

    public function teamLeads()
    {
        return $this->belongsToMany(Pool::class, config('helpdesk.tables.agent_pool'))
            ->withPivot('is_team_lead')
            ->withTimestamps()
            ->wherePivot('is_team_lead', 1);
    }
}
