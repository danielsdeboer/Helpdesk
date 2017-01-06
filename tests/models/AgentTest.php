<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Tests\TestCase;

class AgentTest extends TestCase {

    /**
     * @group agent
     * @test
     */
    public function it_belongs_to_a_user()
    {
        $agent = factory(Agent::class)->create();

        $this->assertNotNull($agent->user);
    }
}