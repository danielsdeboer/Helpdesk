<?php

namespace Aviator\Helpdesk\Traits;

/**
 * Trait AutoUuids
 * @package Aviator\Helpdesk
 * @mixin \Illuminate\Database\Eloquent\Model
 */
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
