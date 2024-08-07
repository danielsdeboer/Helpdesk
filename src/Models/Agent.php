<?php

namespace Aviator\Helpdesk\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

/**
 * @property mixed user
 * @property int id
 * @property bool is_super
 * @property \Illuminate\Support\Collection teams
 * @property \Carbon\Carbon created_at
 * @property \Aviator\Helpdesk\Models\Team team
 * @property \Illuminate\Support\Collection teamLeads
 *
 * @method static Builder withTrashed()
 */
class Agent extends AbstractModel
{
    use Notifiable;
    use SoftDeletes;

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
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->userModelName = config('helpdesk.userModel');
    }

    /*
     * Public Api
     */

    public function getUserName(): string
    {
        return $this->user->name ?? '[Deleted]';
    }

    /**
     * Route notifications for the mail channel.
     */
    public function routeNotificationForMail(): string
    {
        $email = config('helpdesk.userModelEmailColumn');

        return $this->user->$email;
    }

    /**
     * Assign a ticket to this agent.
     *
     * @param bool $public
     * @return $this
     */
    public function assign(Ticket $ticket, self|null $assigner = null, $public = true)
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
     */
    public function makeTeamLeadOf(Team $team): self
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
     * Remove a team lead.
     */
    public function removeTeamLeadOf(Team $team): self
    {
        $this->teams()->detach($team->id);

        $this->teams()->attach($team->id, [
            'is_team_lead' => 0,
        ]);

        return $this;
    }

    /**
     * Add the agent to a team.
     */
    public function addToTeam(Team $team): self
    {
        $this->teams()->attach($team->id);

        return $this;
    }

    /**
     * Remove the agent from a team.
     */
    public function removeFromTeam(Team $team): self
    {
        $this->teams()->detach($team->id);

        return $this;
    }

    /**
     * Add the agent to multiple teams.
     */
    public function addToTeams(array $teams): self
    {
        foreach ($teams as $team) {
            $this->teams()->attach($team);
        }

        return $this;
    }

    /**
     * Remove the agent from multiple teams.
     */
    public function removeFromTeams(array $teams): self
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
     */
    public function isMemberOf(Team $team): bool
    {
        return $team->agents->pluck('id')->contains($this->id);
    }

    public function isLeadOf(Team $team): bool
    {
        return $this->teamLeads->pluck('id')->contains($team->id);
    }

    public function isLeadFor(Ticket $ticket): bool
    {
        return $ticket->teamAssignment
            && $this->isLeadOf($ticket->teamAssignment->team);
    }

    /**
     * Check if the user is a supervisor.
     */
    public function isSuper(): bool
    {
        return (bool) $this->is_super;
    }

    /*
     * Scopes
     */

    /**
     * Scope to agents in a particular team.
     *
     * @return Builder
     */
    public function scopeInTeam(Builder $query, Team $team)
    {
        return $query->whereHas('teams', function (Builder $query) use ($team) {
            $query->where('team_id', $team->id);
        });
    }

    /**
     * Get all agents except the currently signed in agent.
     *
     * @return $this|Builder
     */
    public function scopeExceptAuthorized(Builder $query)
    {
        if (auth()->user() && auth()->user()->agent) {
            return $query->where(
                $this->table . '.id',
                '!=',
                auth()->user()->agent->id
            );
        }

        return $query;
    }

    /**
     * Get all enabled agents.
     *
     * @return $this|Builder
     */
    public function scopeEnabled(Builder $query)
    {
        return $query->whereNull($this->table . '.is_disabled');
    }

    /**
     * Get all disabled agents.
     *
     * @return $this|Builder
     */
    public function scopeDisabled(Builder $query)
    {
        return $query->whereNotNull($this->table . '.is_disabled');
    }

    /*
     * Relationships
     */

    public function user(): BelongsTo
    {
        return $this->belongsTo($this->userModelName);
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, config('helpdesk.tables.agent_team'))
            ->withPivot('is_team_lead')
            ->withTimestamps();
    }

    public function teamLeads(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, config('helpdesk.tables.agent_team'))
            ->withPivot('is_team_lead')
            ->withTimestamps()
            ->wherePivot('is_team_lead', 1);
    }
}
