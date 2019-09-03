<?php

namespace Aviator\Helpdesk\Traits;

use Aviator\Helpdesk\Models\Agent;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait HasAgentRelation
{
    /**
     * The agent relationship.
     * @return HasOne
     */
    public function agent (): HasOne
    {
        return $this->hasOne(Agent::class);
    }
}
