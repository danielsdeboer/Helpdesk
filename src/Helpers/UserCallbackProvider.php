<?php

namespace Aviator\Helpdesk\Helpers;

use Aviator\Helpdesk\Interfaces\HasUserCallback;
use Illuminate\Database\Eloquent\Builder;
use Closure;

class UserCallbackProvider implements HasUserCallback
{
    /**
     * Get a callback to filter users.
     * @return \Closure
     */
    public function getUserCallback () : Closure
    {
        return function (Builder $query) {
            $query->where('is_internal', 1);
        };
    }
}
