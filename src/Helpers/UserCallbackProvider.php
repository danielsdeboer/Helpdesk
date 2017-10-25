<?php

namespace Aviator\Helpdesk\Helpers;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Aviator\Helpdesk\Interfaces\HasUserCallback;

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
