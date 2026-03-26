<?php

namespace Aviator\Helpdesk\Tests\Feature\Models;

use Aviator\Helpdesk\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class TeamTest extends TestCase
{
    #[Test]
    public function adding_multiple_team_leads()
    {
        $team = $this->make->team;
        $agent1 = $this->make->agent;
        $agent2 = $this->make->agent;

        $agent1->makeTeamLeadOf($team);
        $agent2->makeTeamLeadOf($team);

        $this->assertEquals(2, $team->teamLeads->count());
    }

    #[Test]
    public function a_team_has_many_agents()
    {
        $team = $this->make->team;
        $agent = $this->make->agent;
        $agent2 = $this->make->agent;

        $team->agents()->attach([$agent->id, $agent2->id]);

        $this->assertEquals(2, $team->agents->count());
    }

    #[Test]
    public function theIsTeamLeadMethodReturnsTrueIfAnAgentIsALeadOfThatTeam()
    {
        $team = $this->make->team;
        $agent = $this->make->agent;

        $agent->makeTeamLeadOf($team);

        $this->assertTrue($team->isTeamLead($agent));
    }

    #[Test]
    public function theIsTeamLeadMethodReturnsFalseIfAnAgentIsNotALeadOfThatTeam()
    {
        $team = $this->make->team;
        $agent = $this->make->agent;

        $this->assertFalse($team->isTeamLead($agent));
    }
}
