<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Agent;

class Bug62Test extends AdminBase
{
    /**
     * @test
     */
    public function administrators_are_agents ()
    {
        $agent = factory(Agent::class)->states('isSuper')->create();

        $this->assertTrue($agent->isSuper());
    }
}
