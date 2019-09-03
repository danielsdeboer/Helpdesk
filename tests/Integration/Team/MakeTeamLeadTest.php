<?php

namespace Aviator\Helpdesk\Tests\Integration\Team;

use Aviator\Helpdesk\Tests\TestCase;

class MakeTeamLeadTest extends TestCase
{
    /** @test */
    public function make_team_lead_creates_new_team_lead()
    {
        $team = $this->make->team;
        $super = $this->make->super;
        $agent2 = $this->make->agent;

        $super->addToTeam($team);
        $agent2->addToTeam($team);

        $this->be($super->user);

        $response = $this->post(
            'helpdesk/admin/team-members/make-team-lead',
            [
                'agent_id' => $super->id,
                'team_id' => $team->id,
                'from' => 'agent',
            ]
        );

        $response->assertRedirect('helpdesk/admin/teams/1');
        $this->assertTrue($super->isLeadOf($team));
        $this->assertFalse($agent2->isLeadOf($team));
    }
}
