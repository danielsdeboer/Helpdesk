<?php

namespace Aviator\Helpdesk\Models;

use Aviator\Helpdesk\Models\ActionBase;
use Illuminate\Database\Eloquent\Model;

class Pool extends Model
{
    protected $guarded = [];

    /**
     * Set the table name from the Helpdesk config
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('helpdesk.tables.pool'));
    }

    public function teamLead() {
        return $this->belongsTo(config('helpdesk.userModel'), 'team_lead');
    }
}