<?php

namespace Aviator\Helpdesk\Tests\Admin\TeamMembers;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Tests\AdminBase;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class AddTest extends AdminBase
{
    const VERB = 'POST';
    const URI = 'helpdesk/admin/team-members/add';

    /**
     * @group acc
     * @group acc.admin
     * @group acc.admin.member
     * @group acc.admin.member.add
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
     * @group acc.admin.member.add
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
     * @group acc.admin.member.add
     * @test
     */
    public function an_agent_can_be_added_to_a_team()
    {
        $agent = $this->makeAgent();
        $team = $this->makeTeam();

        $this->beSuper();
        $this->callUri([
            'agent_id' => $agent->id,
            'team_id' => $team->id,
            'from' => 'agent'
        ]);

        $this->assertEquals(1, $agent->teams->count());
    }

    /**
     * @group acc
     * @group acc.admin
     * @group acc.admin.member
     * @group acc.admin.member.add
     * @test
     */
    public function an_agent_cant_be_added_to_a_team_more_than_once()
    {
        $agent = $this->makeAgent();
        $team = $this->makeTeam();

        $agent->addToTeam($team);

        try {
            $agent->addToTeam($team);
        } catch (QueryException $e) {
            return;
        }

        $this->fail('you should not be able to add a user to a team twice');
    }
}
