<?php

namespace Aviator\Helpdesk\Models;

/**
 * @property mixed assignee
 * @property mixed ticket_id
 * @property mixed id
 * @property mixed ticket
 */
class Assignment extends ActionBase
{
    public function assignee()
    {
        return $this->belongsTo(Agent::class, 'assigned_to');
    }

    /**
     * Set the table name from the Helpdesk config.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('helpdesk.tables.assignments'));
    }
}
