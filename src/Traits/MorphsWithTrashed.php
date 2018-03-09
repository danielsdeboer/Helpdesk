<?php

namespace Aviator\Helpdesk\Traits;

use Illuminate\Database\Eloquent\Relations\MorphTo;

trait MorphsWithTrashed
{
    /**
     * @param string $name
     * @return MorphTo
     */
    protected function morphToWithTrashed (string $name) : MorphTo
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return $this->morphTo($name)->withTrashed();
    }
}