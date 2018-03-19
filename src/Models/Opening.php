<?php

namespace Aviator\Helpdesk\Models;

/**
 * @property \Aviator\Helpdesk\Models\Action action
 * @property \Aviator\Helpdesk\Models\Ticket ticket
 * @property string note
 */
class Opening extends ActionBase
{
    /** @var string */
    protected $configKey = 'helpdesk.tables.openings';
}
