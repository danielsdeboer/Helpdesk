<?php

namespace Aviator\Helpdesk\Tests\Traits;

use Aviator\Helpdesk\Models\Agent;

trait CreatesAgents
{
    /**
     * @return \Aviator\Helpdesk\Models\Agent
     */
    protected function createAgent()
    {
        return factory(Agent::class)->create();
    }
}
