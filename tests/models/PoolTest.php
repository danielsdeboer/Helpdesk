<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Pool;
use Aviator\Helpdesk\Models\Agent;

class PoolTest extends TestCase
{
    /**
     * @group model
     * @group model.pool
     * @test
     */
    public function aPoolHasTeamLeads()
    {
        $pool = factory(Pool::class)->create();
        $agent = factory(Agent::class)->create();
        $agent2 = factory(Agent::class)->create();

        $agent->makeTeamLeadOf($pool);
        $agent2->makeTeamLeadOf($pool);

        $this->assertEquals(2, $pool->teamLeads->count());
    }

    /**
     * @group model
     * @group model.pool
     * @test
     */
    public function aPoolHasManyAgents()
    {
        $pool = factory(Pool::class)->create();
        $agent = factory(Agent::class)->create();
        $agent2 = factory(Agent::class)->create();

        $pool->agents()->attach([$agent->id, $agent2->id]);

        $this->assertEquals(2, $pool->agents->count());
    }

    /**
     * @group model
     * @group model.pool
     * @test
     */
    public function theIsTeamLeadMethodReturnsTrueIfAnAgentIsALeadOfThatTeam()
    {
        $pool = factory(Pool::class)->create();
        $agent = factory(Agent::class)->create();

        $agent->makeTeamLeadOf($pool);

        $this->assertTrue($pool->isTeamLead($agent));
    }

    /**
     * @group model
     * @group model.pool
     * @test
     */
    public function theIsTeamLeadMethodReturnsFalseIfAnAgentIsNotALeadOfThatTeam()
    {
        $pool = factory(Pool::class)->create();
        $agent = factory(Agent::class)->create();

        $this->assertFalse($pool->isTeamLead($agent));
    }
}
