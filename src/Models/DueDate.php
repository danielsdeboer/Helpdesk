<?php

namespace Aviator\Helpdesk\Models;

use Carbon\Carbon;

/**
 * @property \Aviator\Helpdesk\Models\Action action
 * @property \Carbon\Carbon due_on
 * @property \Aviator\Helpdesk\Models\Agent agent
 */
class DueDate extends ActionBase
{
    /** @var array */
    protected $casts = [
        'due_on' => 'date',
        'is_visible' => 'boolean',
    ];

    /** @var string */
    protected $configKey = 'helpdesk.tables.due_dates';

    /**
     * Enforce the dateString format for due_on. This is useful
     * for sqlite which doesn't have a date type.
     * @param mixed $value
     * @return void
     */
    public function setDueOnAttribute ($value)
    {
        $this->attributes['due_on'] = Carbon::parse($value)->toDateString();
    }
}
