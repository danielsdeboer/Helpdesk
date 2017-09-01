<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Tests\Traits\CallsAs;
use Aviator\Helpdesk\Tests\Traits\VisitsAs;
use Aviator\Helpdesk\Tests\Traits\CreatesUsers;
use Aviator\Helpdesk\Tests\Traits\CreatesAgents;

class AgentDashboardTest extends TestCase
{
    /*
     * Setup -----------------------------------------------------------------------------------------------------------
     */

    use CreatesAgents, CreatesUsers, CallsAs, VisitsAs;

    protected $dash = 'helpdesk/dashboard/agent';

    /*
     * Tests -----------------------------------------------------------------------------------------------------------
     */

    /**
     * @group acc
     * @group acc.dash
     * @group acc.dash.agents
     * @test
     */
    public function a_user_cannot_visit_the_agent_dashboard()
    {
        $user = $this->createUser();

        $this->callAs($user, $this->dash);

        $this->assertResponseStatus(403);
    }

    /**
     * @group acc
     * @group acc.dash
     * @group acc.dash.agents
     * @test
     */
    public function an_agent_can_visit_their_dashboard()
    {
        $user = $this->createAgent()->user;

        $this->callAs($user, $this->dash);

        $this->assertResponseOk();
    }

    /**
     * @group acc
     * @group acc.dash
     * @group acc.dash.agents
     * @test
     */
    public function an_agent_can_see_their_dashboard()
    {
        $user = $this->createAgent()->user;

        $this->visitAs($user, $this->dash)
            ->see('Helpdesk')
            ->see('Assigned to team')
            ->see('Overdue')
            ->see('Open')
            ->see('Nothing to see here!')
            ->dontSee('id="header-tab-admin"');
    }

    /**
     * @group acc
     * @group acc.dash
     * @group acc.dash.agents
     * @test
     */
    public function agent_dashboard_has_collaborating_list()
    {
        $user = $this->createAgent()->user;

        $this->visitAs($user, $this->dash)
            ->see('id="collab-count-title"')
            ->see('id="collab-count-number"')
            ->see('id="collab-title"')
            ->see('id="collab-no-results"');
    }
}
