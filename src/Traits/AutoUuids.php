<?php

namespace Aviator\Helpdesk\Traits;

trait AutoUuids
{
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = strtolower(str_random(32));
        });
    }
}
