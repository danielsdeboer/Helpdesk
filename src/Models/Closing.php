<?php

namespace Aviator\Helpdesk\Models;

/**
 * @property \Aviator\Helpdesk\Models\Action action
 * @property \Aviator\Helpdesk\Models\Ticket ticket
 */
class Closing extends ActionBase
{
    /**
     * Set the table name from the Helpdesk config.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('helpdesk.tables.closings'));
    }
}
