<?php

namespace Aviator\Helpdesk\Traits;

use Aviator\Helpdesk\Models\Agent;

/**
 * Trait HasAgentRelation
 * @package Aviator\Helpdesk
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait HasAgentRelation
{
    public function agent ()
    {
        return $this->hasOne(Agent::class);
    }
}
