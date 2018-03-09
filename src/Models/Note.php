<?php

namespace Aviator\Helpdesk\Models;

/**
 * @property \Aviator\Helpdesk\Models\Action action
 */
class Note extends ActionBase
{
    /** @var string */
    protected $configKey = 'helpdesk.tables.notes';
}
