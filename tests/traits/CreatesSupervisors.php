<?php

namespace Aviator\Helpdesk\Tests\Traits;

use Aviator\Helpdesk\Models\Agent;

trait CreatesSupervisors
{
    /**
     * @return \Aviator\Helpdesk\Models\Agent
     */
    protected function createSupervisor()
    {
        return factory(Agent::class)->states('isSuper')->create();
    }
}
