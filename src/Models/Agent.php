<?php

namespace Aviator\Helpdesk\Models;

use function foo\func;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
class Agent extends AbstractModel
{
    use SoftDeletes, Notifiable;

    /** @var \Illuminate\Database\Eloquent\Model */
    protected $userModelName;

    /** @var string */
    protected $configKey = 'helpdesk.tables.agents';

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
    public function __construct (array $attributes = [])
    {
        parent::__construct($attributes);

        $this->userModelName = config('helpdesk.userModel');
    }

    /*
     * Public Api
     */

    /**
     * Route notifications for the mail channel.
     * @return string
     */
    public function routeNotificationForMail () : string
    {
        $email = config('helpdesk.userModelEmailColumn');

        return $this->user->$email;
    }

    /**
     * Assign a ticket to this agent.
     * @param \Aviator\Helpdesk\Models\Ticket $ticket
     * @param \Aviator\Helpdesk\Models\Agent|null $assigner
     * @param bool $public
     * @return $this
     */
    public function assign (Ticket $ticket, Agent $assigner = null, $public = true)
    {
        Assignment::query()
            ->create([
                'ticket_id' => $ticket->id,
                'assigned_to' => $this->id,
                'agent_id' => $assigner->id ?? null,
                'is_visible' => $public,
            ]);

        return $this;
    }

    /**
     * Make the Agent a team lead.
     * @param \Aviator\Helpdesk\Models\Team $team
     * @return Agent
     */
    public function makeTeamLeadOf (Team $team) : self
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
     * @return Agent
     */
    public function removeTeamLeadOf (Team $team) : self
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
     * @return Agent
     */
    public function addToTeam (Team $team) : self
    {
        $this->teams()->attach($team->id);

        return $this;
    }

    /**
     * Remove the agent from a team.
     * @param Team $team
     * @return Agent
     */
    public function removeFromTeam (Team $team) : self
    {
        $this->teams()->detach($team->id);

        return $this;
    }

    /**
     * Add the agent to multiple teams.
     * @param array $teams
     * @return Agent
     */
    public function addToTeams (array $teams) : self
    {
        foreach ($teams as $team) {
            $this->teams()->attach($team);
        }

        return $this;
    }

    /**
     * Remove the agent from multiple teams.
     * @param array $teams
     * @return Agent
     */
    public function removeFromTeams (array $teams) : self
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
     * @param Team $team
     * @return bool
     */
    public function isMemberOf (Team $team) : bool
    {
        return $team->agents->pluck('id')->contains($this->id);
    }

    /**
     * @param Team $team
     * @return bool
     */
    public function isLeadOf (Team $team) : bool
    {
        return $this->teamLeads->pluck('id')->contains($team->id);
    }

    /**
     * @param Ticket $ticket
     * @return bool
     */
    public function isLeadFor (Ticket $ticket) : bool
    {
        return $ticket->teamAssignment
            && $this->isLeadOf($ticket->teamAssignment->team);
    }

    /**
     * Check if the user is a supervisor.
     * @return bool
     */
    public function isSuper () : bool
    {
        return (bool) $this->is_super;
    }

    /*
     * Scopes
     */

    /**
     * Scope to agents in a particular team.
     * @param Builder $query
     * @param Team $team
     * @return Builder
     */
    public function scopeInTeam (Builder $query, Team $team)
    {
        return $query->whereHas('teams', function (Builder $query) use ($team) {
            $query->where('team_id', $team->id);
        });
    }

    /**
     * Get all agents except the currently signed in agent.
     * @param Builder $query
     * @return $this|Builder
     */
    public function scopeExceptAuthorized (Builder $query)
    {
        if (auth()->user() && auth()->user()->agent) {
            return $query->where(
                $this->table.'.id',
                '!=',
                auth()->user()->agent->id
            );
        }

        return $query;
    }

    /*
     * Relationships
     */

    /**
     * @return BelongsTo
     */
    public function user () : BelongsTo
    {
        return $this->belongsTo($this->userModelName);
    }

    /**
     * @return BelongsToMany
     */
    public function teams() : BelongsToMany
    {
        return $this->belongsToMany(Team::class, config('helpdesk.tables.agent_team'))
            ->withPivot('is_team_lead')
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany
     */
    public function teamLeads () : BelongsToMany
    {
        return $this->belongsToMany(Team::class, config('helpdesk.tables.agent_team'))
            ->withPivot('is_team_lead')
            ->withTimestamps()
            ->wherePivot('is_team_lead', 1);
    }
}
