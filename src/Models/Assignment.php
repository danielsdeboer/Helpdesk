<?php

namespace Aviator\Helpdesk\Models;

use Aviator\Helpdesk\Models\ActionBase;

class Assignment extends ActionBase
{
    /**
     * Overridden as assignment belong to agents, not users
     */
    public function creator() {
        return $this->belongsTo(Agent::class, 'created_by');
    }

    public function assignee() {
        return $this->belongsTo(Agent::class, 'assigned_to');
    }

    /**
     * Set the table name from the Helpdesk config
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('helpdesk.tables.assignments'));
    }
}