<?php

namespace Aviator\Helpdesk\Traits;

use Illuminate\Database\Eloquent\Relations\MorphTo;

trait MorphsWithTrashed
{
    protected function morphToWithTrashed(string $name): MorphTo
    {
        /* @noinspection PhpUndefinedMethodInspection */
        return $this->morphTo($name)->withTrashed();
    }
}
