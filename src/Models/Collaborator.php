<?php

namespace Aviator\Helpdesk\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property mixed ticket_id
 * @property mixed id
 * @property \Aviator\Helpdesk\Models\Agent agent
 * @property \Aviator\Helpdesk\Models\Ticket ticket
 * @property \Aviator\Helpdesk\Models\Action action
 * @property mixed createdBy
 */
class Collaborator extends ActionBase
{
    /** @var string */
    protected $configKey = 'helpdesk.tables.collaborators';

    /**
     * @return BelongsTo
     */
    public function createdBy () : BelongsTo
    {
        return $this->belongsTo(Agent::class, 'created_by');
    }
}
