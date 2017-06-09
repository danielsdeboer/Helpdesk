<?php

namespace Aviator\Helpdesk\Tests\Admin\TeamMembers;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Tests\AdminBase;

class RemoveTest extends AdminBase
{
    const VERB = 'POST';
    const URI = 'helpdesk/admin/team-members/remove';

    /**
     * @group acc
     * @group acc.admin
     * @group acc.admin.member
     * @group acc.admin.member.remove
     * @test
     */
    public function access_test()
    {
        $this->noGuests();
        $this->noUsers();
        $this->noAgents();
    }

    /**
     * @group acc
     * @group acc.admin
     * @group acc.admin.member
     * @group acc.admin.member.remove
     * @test
     */
    public function the_request_requires_three_parameters()
    {
        $agent = $this->makeAgent();
        $team = $this->makeTeam();

        $this->beSuper();
        $this->callUri();

        $this->assertValidationFailed(['agent_id', 'team_id', 'from']);
    }

    /**
     * @group acc
     * @group acc.admin
     * @group acc.admin.member
     * @group acc.admin.member.remove
     * @test
     */
    public function an_agent_can_be_removed_from_a_team()
    {
        $agent = $this->makeAgent();
        $team = $this->makeTeam();

        $agent->addToTeam($team);

        $this->beSuper();
        $this->callUri([
            'agent_id' => $agent->id,
            'team_id' => $team->id,
            'from' => 'agent',
        ]);

        $agent = $agent->fresh();

        $this->assertEquals(0, $agent->teams->count());
    }
}
