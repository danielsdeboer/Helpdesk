<?php

namespace Aviator\Helpdesk\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property mixed id
 * @property string name
 * @property \Illuminate\Support\Collection teamLeads
 * @property \Illuminate\Support\Collection agents
 * @property mixed team_lead
 * @property mixed teamLead
 */
class Team extends AbstractModel
{
    use SoftDeletes;

    /** @var string */
    protected $configKey = 'helpdesk.tables.teams';

    /** @var array */
    protected $guarded = [];

    ////////////////
    // Public API //
    ////////////////

    /**
     * Check if an agent is a team lead of this team.
     * @param  Agent   $agent
     * @return bool
     */
    public function isTeamLead(Agent $agent)
    {
        return $agent->teamLeads && $agent->teamLeads->pluck('id')->contains($this->id);
    }

    ///////////////////
    // Relationships //
    ///////////////////

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function agents()
    {
        return $this->belongsToMany(Agent::class, config('helpdesk.tables.agent_team'))
            ->withPivot('is_team_lead')
            ->withTimestamps();
    }

    public function teamLeads()
    {
        return $this->belongsToMany(Agent::class, config('helpdesk.tables.agent_team'))
            ->withPivot('is_team_lead')
            ->withTimestamps()
            ->wherePivot('is_team_lead', 1);
    }
}
