<?php

namespace Aviator\Helpdesk\Tests\Admin\TeamMembers;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Tests\AdminBase;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

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
}
