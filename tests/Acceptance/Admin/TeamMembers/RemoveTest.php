<?php

namespace Aviator\Helpdesk\Tests\Acceptance\Admin\TeamMembers;

use Aviator\Helpdesk\Tests\AdminBase;

class RemoveTest extends AdminBase
{
    const VERB = 'POST';
    const URI = 'helpdesk/admin/team-members/remove';

    /** @test */
    public function access_test ()
    {
        $this->noGuests();
        $this->noUsers();
        $this->noAgents();
    }

    /** @test */
    public function the_request_requires_three_parameters ()
    {
        $agent = $this->make->agent;
        $team = $this->make->team;

        $this->beSuper();
        $this->post(self::URI);

        $this->assertValidationFailed(['agent_id', 'team_id', 'from']);
    }

    /** @test */
    public function an_agent_can_be_removed_from_a_team()
    {
        $agent = $this->make->agent;
        $team = $this->make->team;

        $agent->addToTeam($team);

        $this->beSuper();
        $this->post(self::URI, [
            'agent_id' => $agent->id,
            'team_id' => $team->id,
            'from' => 'agent',
        ]);

        $agent = $agent->fresh();

        $this->assertEquals(0, $agent->teams->count());
    }
}
