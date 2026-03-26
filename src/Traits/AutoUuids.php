<?php

namespace Aviator\Helpdesk\Traits;

use Illuminate\Support\Str;

trait AutoUuids
{
    /**
     * Boot the trait.
     */
    protected static function bootAutoUuids()
    {
        static::creating(function ($model) {
            $model->uuid = strtolower(Str::random(32));
        });
    }
}
