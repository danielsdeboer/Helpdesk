<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Team;
use Aviator\Helpdesk\Models\Agent;

class TeamTest extends TestCase
{
    /**
     * @group model
     * @group model.team
     * @test
     */
    public function adding_multiple_team_leads ()
    {
        $team = $this->make->team;
        $agent = factory(Agent::class)->create();
        $agent2 = factory(Agent::class)->create();

        $agent->makeTeamLeadOf($team);
        $agent2->makeTeamLeadOf($team);

        $this->assertEquals(2, $team->teamLeads->count());
    }

    /**
     * @group model
     * @group model.team
     * @test
     */
    public function aTeamHasManyAgents()
    {
        $team = $this->make->team;
        $agent = factory(Agent::class)->create();
        $agent2 = factory(Agent::class)->create();

        $team->agents()->attach([$agent->id, $agent2->id]);

        $this->assertEquals(2, $team->agents->count());
    }

    /**
     * @group model
     * @group model.team
     * @test
     */
    public function theIsTeamLeadMethodReturnsTrueIfAnAgentIsALeadOfThatTeam()
    {
        $team = $this->make->team;
        $agent = factory(Agent::class)->create();

        $agent->makeTeamLeadOf($team);

        $this->assertTrue($team->isTeamLead($agent));
    }

    /**
     * @group model
     * @group model.team
     * @test
     */
    public function theIsTeamLeadMethodReturnsFalseIfAnAgentIsNotALeadOfThatTeam()
    {
        $team = $this->make->team;
        $agent = factory(Agent::class)->create();

        $this->assertFalse($team->isTeamLead($agent));
    }
}
