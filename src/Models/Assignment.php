<?php

namespace Aviator\Helpdesk\Models;

use Aviator\Helpdesk\Models\ActionBase;

class Assignment extends ActionBase
{
    public function assignee() {
        return $this->belongsTo(config('helpdesk.userModel'), 'assigned_to');
    }

    public function action()
    {
        return $this->morphOne(Action::class, 'object');
    }
}