<?php

namespace Aviator\Helpdesk\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property \Aviator\Helpdesk\Models\Team team
 * @property \Aviator\Helpdesk\Models\Action action
 * @property int ticket_id
 * @property int id
 * @property \Aviator\Helpdesk\Models\Ticket ticket
 * @property \Aviator\Helpdesk\Models\Agent agent
 */
class TeamAssignment extends ActionBase
{
    /** @var string */
    protected $configKey = 'helpdesk.tables.team_assignments';

    /**
     * @return BelongsTo
     */
    public function team (): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
