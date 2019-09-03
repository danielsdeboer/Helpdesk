<?php

namespace Aviator\Helpdesk\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property mixed assignee
 * @property mixed ticket_id
 * @property mixed id
 * @property mixed ticket
 * @property mixed assigned_to
 * @property string action
 * @property \Aviator\Helpdesk\Models\Agent agent
 */
class Assignment extends ActionBase
{
    /** @var string */
    protected $configKey = 'helpdesk.tables.assignments';

    /**
     * @return BelongsTo
     */
    public function assignee (): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'assigned_to');
    }
}
