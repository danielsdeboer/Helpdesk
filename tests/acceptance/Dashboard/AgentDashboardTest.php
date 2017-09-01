<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Tests\Traits\CreatesAgents;
use Aviator\Helpdesk\Tests\Traits\CreatesUsers;

class AgentDashboardTest extends TestCase
{
    use CreatesAgents, CreatesUsers;

    /**
     * @group acc
     * @group acc.dash
     * @group acc.dash.agents
     * @test
     */
    public function a_user_cannot_visit_the_agent_dashboard()
    {
        $this->be($this->createUser());

        $this->call('GET', 'helpdesk/dashboard/agent');

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

        $this->be($user);

        $response = $this->call('GET', 'helpdesk/dashboard/agent');

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

        $this->be($user);

        $this->visit('helpdesk/dashboard/agent')
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

        $this->be($user);

        $this->visit('helpdesk/dashboard/agent')
            ->see('<p class="heading">Collaborating On</p>')
            ->see('<p class="title"><strong>0</strong></p>')
            ->see('<h1 class="title" id="collab-title">Collaborating On</h1>')
            ->see('<div class="hero-body is-small has-text-centered" id="collab-no-results">')
            ;
    }
}
