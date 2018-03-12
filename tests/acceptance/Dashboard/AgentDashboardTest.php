<?php

namespace Aviator\Helpdesk\Tests;

class AgentDashboardTest extends BKTestCase
{
    /** @const string */
    const URI = 'helpdesk/dashboard/agent';

    /** @test */
    public function a_user_cannot_visit_the_agent_dashboard ()
    {
        $this->be($this->make->user);
        $this->get(self::URI);

        $this->assertResponseStatus(403);
    }

    /** @test */
    public function an_agent_can_visit_their_dashboard ()
    {
        $this->be($this->make->agent->user);
        $this->get(self::URI);

        $this->assertResponseOk();
    }

    /** @test */
    public function an_agent_can_see_their_dashboard ()
    {
        $this->be($this->make->agent->user);

        $this->visit(self::URI)
            ->see('Helpdesk')
            ->see('Assigned to team')
            ->see('Overdue')
            ->see('Open')
            ->see('Nothing to see here!')
            ->dontSee('id="header-tab-admin"');
    }

    /** @test */
    public function agent_dashboard_has_collaborating_list ()
    {
        $this->be($this->make->agent->user);

        $this->visit(self::URI)
            ->see('id="collab-count-title"')
            ->see('id="collab-count-number"')
            ->see('id="collab-title"')
            ->see('id="collab-no-results"');
    }
}
