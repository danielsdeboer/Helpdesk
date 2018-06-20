<?php

namespace Aviator\Helpdesk\Tests\Integration\Team;

use Aviator\Helpdesk\Tests\TestCase;

class MakeTeamLeadTest extends TestCase
{
    /** @test */
    public function make_team_lead_creates_new_team_lead()
    {
        $team = $this->make->team;
        $agent = $this->make->super;

        $this->be($agent->user);

        $response = $this->post('helpdesk/admin/team-members/make-team-lead',
            [
                'agent_id' => $agent->id,
                'team_id' => $team->id,
                'from' => 'agent'
            ]
        );

        $response->assertRedirect('helpdesk/admin/teams/1');
    }
}
