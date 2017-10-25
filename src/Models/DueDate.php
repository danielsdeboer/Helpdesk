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
    protected $casts = [
        'due_on' => 'date',
    ];

    /**
     * Set the table name from the Helpdesk config.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('helpdesk.tables.due_dates'));
    }

    /**
     * Enforce the dateString format for due_on. This is useful
     * for sqlite which doesn't have a date type.
     * @param mixed $value
     */
    public function setDueOnAttribute($value)
    {
        $this->attributes['due_on'] = Carbon::parse($value)->toDateString();
    }
}
