<?php

namespace Aviator\Helpdesk\Models;

/**
 * @property \Aviator\Helpdesk\Models\Team team
 * @property \Aviator\Helpdesk\Models\Action action
 * @property int ticket_id
 * @property int id
 * @property \Aviator\Helpdesk\Models\Ticket ticket
 */
class TeamAssignment extends ActionBase
{
    /**
     * Set the table name from the Helpdesk config.
     * @param array $attributes
     */
    public function __construct (array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(
            config('helpdesk.tables.team_assignments')
        );
    }

    public function team ()
    {
        return $this->belongsTo(Team::class);
    }
}
