<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Pool;
use Illuminate\Support\Facades\Notification;

class PoolTest extends TestCase {

    /**
     * @group pool
     * @test
     */
    public function a_pool_has_team_leads()
    {
        $pool = factory(Pool::class)->create();
        $agent = factory(Agent::class)->create();
        $agent2 = factory(Agent::class)->create();

        $agent->makeTeamLeadOf($pool);
        $agent2->makeTeamLeadOf($pool);

        $this->assertEquals(2, $pool->teamLeads->count());
    }

    /**
     * @group pool
     * @test
     */
    public function a_pool_has_many_agents()
    {
        $pool = factory(Pool::class)->create();
        $agent = factory(Agent::class)->create();
        $agent2 = factory(Agent::class)->create();

        $pool->agents()->attach([$agent->id, $agent2->id]);

        $this->assertEquals(2, $pool->agents->count());
    }
}