<?php

namespace Aviator\Helpdesk\Models;

/**
 * @property \Aviator\Helpdesk\Models\Action action
 * @property int is_visible
 * @property \Aviator\Helpdesk\Models\Ticket ticket
 * @property Agent agent
 * @property mixed user
 */
class Reply extends ActionBase
{
    /** @var string */
    protected $configKey = 'helpdesk.tables.replies';
}
