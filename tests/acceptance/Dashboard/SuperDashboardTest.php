<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Tests\Traits\CallsAs;
use Aviator\Helpdesk\Tests\Traits\VisitsAs;
use Aviator\Helpdesk\Tests\Traits\CreatesUsers;
use Aviator\Helpdesk\Tests\Traits\CreatesAgents;
use Aviator\Helpdesk\Tests\Traits\CreatesSupervisors;

class SuperDashboardTest extends TestCase
{
    /*
     * Setup -----------------------------------------------------------------------------------------------------------
     */

    use CreatesAgents, CreatesUsers, CreatesSupervisors, CallsAs, VisitsAs;

    protected $dash = 'helpdesk/dashboard/supervisor';

    /*
     * Tests -----------------------------------------------------------------------------------------------------------
     */

    /**
     * @group acc
     * @group acc.dash
     * @group acc.dash.super
     * @test
     */
    public function a_guest_cannot_visit_the_supervisor_dashboard()
    {
        $response = $this->call('GET', 'helpdesk/dashboard/supervisor');

        $this->assertResponseStatus(403);
    }

    /**
     * @group acc
     * @group acc.dash
     * @group acc.dash.super
     * @test
     */
    public function an_agent_cannot_visit_the_supervisor_dashboard()
    {
        $user = $this->createAgent()->user;

        $this->callAs($user, $this->dash);

        $this->assertResponseStatus(403);
    }

    /**
     * @group acc
     * @group acc.dash
     * @group acc.dash.super
     * @test
     */
    public function a_supervisor_can_visit_their_dashboard()
    {
        $user = $this->createSupervisor()->user;

        $this->callAs($user, $this->dash);

        $this->assertResponseOk();
    }

    /**
     * @group acc
     * @group acc.dash
     * @group acc.dash.super
     * @test
     */
    public function a_supervisor_can_see_their_dashboard()
    {
        $user = $this->createSupervisor()->user;

        $this->visitAs($user, $this->dash)
            ->see('Helpdesk')
            ->see('Unassigned')
            ->see('Overdue')
            ->see('Open')
            ->see('Nothing to see here!')
            ->see('id="header-tab-admin"');
    }

    /**
     * @group acc
     * @group acc.dash
     * @group acc.dash.agents
     * @test
     */
    public function agent_dashboard_has_collaborating_list()
    {
        $user = $this->createSupervisor()->user;

        $this->visitAs($user, $this->dash)
            ->see('id="collab-count-title"')
            ->see('id="collab-count-number"')
            ->see('id="collab-title"')
            ->see('id="collab-no-results"');
    }
}
