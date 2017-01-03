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
}