<?php

namespace Aviator\Helpdesk\Tests\Acceptance\Dashboard\Acceptance\Tickets\Acceptance\Acceptance\Acceptance\Acceptance\Acceptance\Acceptance\Acceptance\Acceptance\Acceptance\Tickets\Integration\Team;

use Aviator\Helpdesk\Tests\TestCase;

class ShowTest extends TestCase
{
    /** @test */
    public function it_shows_a_teams_agents()
    {
        $team = $this->make->team;
        $team2 = $this->make->team;
        $team3 = $this->make->team;
        $agent = $this->make->super;

        $this->be($agent->user);

        $agent->addToTeam($team2);
        $agent->makeTeamLeadOf($team2);

        $response = $this->get('helpdesk/admin/teams/2');

        $response->assertSeeText('Team Lead');
    }
}
