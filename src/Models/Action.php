<?php

namespace Aviator\Helpdesk\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Action extends Model
{
    use SoftDeletes;

    public function subject()
    {
        return $this->morphTo()->withTrashed();
    }

    public function object()
    {
        return $this->morphTo()->withTrashed();
    }

    /**
     * Set the table name from the Helpdesk config.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('helpdesk.tables.actions'));
    }
}
