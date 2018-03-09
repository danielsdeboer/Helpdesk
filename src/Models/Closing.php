<?php

namespace Aviator\Helpdesk\Models;

/**
 * @property \Aviator\Helpdesk\Models\Action action
 * @property \Aviator\Helpdesk\Models\Ticket ticket
 * @property string note
 */
class Closing extends ActionBase
{
    /** @var string */
    protected $configKey = 'helpdesk.tables.closings';
}
