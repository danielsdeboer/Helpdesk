<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Agent;

class AgentDashboardTest extends TestCase
{
    /**
     * @group feature
     * @group feature.dash
     * @group feature.dash.agents
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
     * @group feature.dash
     * @group feature.dash.agents
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
     * @group feature.dash
     * @group feature.dash.agents
     * @test
     */
    public function an_agent_can_see_their_dashboard()
    {
        $this->be(factory(Agent::class)->create()->user);

        $this->visit('helpdesk/dashboard/agent')
            ->see('Helpdesk')
            ->see('Assigned to team')
            ->see('Overdue')
            ->see('Open')
            ->see('Nothing to see here!')
            ->dontSee('id="header-tab-admin"');
    }
}
