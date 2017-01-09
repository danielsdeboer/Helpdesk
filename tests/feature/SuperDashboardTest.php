<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Pool;
use Aviator\Helpdesk\Models\Ticket;

class SuperDashboardTest extends TestCase
{
    /**
     * @group feature
     * @group dashboard
     * @group super
     * @test
     */
    public function a_guest_cannot_visit_the_supervisor_dashboard()
    {
        $response = $this->call('GET', 'helpdesk/dashboard/supervisor');

        $this->assertResponseStatus(403);
    }

    /**
     * @group feature
     * @group dashboard
     * @group super
     * @test
     */
    public function an_agent_cannot_visit_the_supervisor_dashboard()
    {
        $this->be(factory(Agent::class)->create()->user);

        $response = $this->call('GET', 'helpdesk/dashboard/supervisor');

        $this->assertResponseStatus(403);
    }

    /**
     * @group feature
     * @group dashboard
     * @group super
     * @test
     */
    public function a_supervisor_can_visit_their_dashboard()
    {
        $this->be(factory(Agent::class)->states('isSuper')->create()->user);

        $this->call('GET', 'helpdesk/dashboard/supervisor');

        $this->assertResponseOk();
    }

    /**
     * @group feature
     * @group dashboard
     * @group super
     * @test
    */
    public function a_supervisor_can_see_their_dashboard()
    {
        $this->be(factory(Agent::class)->states('isSuper')->create()->user);

        $this->visit('helpdesk/dashboard/supervisor')
            ->see('Helpdesk')
            ->see('Unassigned')
            ->see('Overdue')
            ->see('Open')
            ->see('Nothing to see here!');
    }
}