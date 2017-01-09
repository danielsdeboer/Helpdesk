<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Pool;
use Aviator\Helpdesk\Models\Ticket;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AgentDashboardTest extends TestCase
{
    /**
     * @group feature
     * @group dashboard
     * @test
     */
    public function a_user_cannot_visit_the_agent_dashboard()
    {
        $this->be(factory(User::class)->create());

        $this->call('GET', 'helpdesk/dashboard/agent');

        $this->assertResponseStatus(403);
    }

    /**
     * @group feature
     * @group dashboard
     * @test
     */
    public function an_agent_can_visit_their_dashboard()
    {
        $this->be(factory(Agent::class)->create()->user);

        $response = $this->call('GET', 'helpdesk/dashboard/agent');

        $this->assertResponseOk();
    }

    /**
     * @group feature
     * @group dashboard
     * @test
     */
    public function the_dashboard_returns_the_correct_json_structure()
    {
        $this->be(factory(Agent::class)->create()->user);

        $response = $this->call('get', 'helpdesk/dashboard/agent');

        $this->assertResponseOk();
        $this->seeJsonStructure([
            'team',
            'overdue',
            'agent',
        ]);
    }
}