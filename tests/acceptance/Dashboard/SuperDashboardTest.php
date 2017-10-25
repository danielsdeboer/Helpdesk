<?php

namespace Aviator\Helpdesk\Tests;

class SuperDashboardTest extends TestCase
{
    /** @const string */
    const URI = 'helpdesk/dashboard/supervisor';

    /** @test */
    public function a_guest_cannot_visit_the_supervisor_dashboard()
    {
        $this->get(self::URI);

        $this->assertResponseStatus(403);
    }

    /** @test */
    public function an_agent_cannot_visit_the_supervisor_dashboard()
    {
        $this->be($this->make->agent->user);
        $this->get(self::URI);

        $this->assertResponseStatus(403);
    }

    /** @test */
    public function a_supervisor_can_visit_their_dashboard()
    {
        $this->be($this->make->super->user);
        $this->get(self::URI);

        $this->assertResponseOk();
    }

    /** @test */
    public function a_supervisor_can_see_their_dashboard()
    {
        $this->be($this->make->super->user);

        $this->visit(self::URI)
            ->see('Helpdesk')
            ->see('Unassigned')
            ->see('Overdue')
            ->see('Open')
            ->see('Nothing to see here!')
            ->see('id="header-tab-admin"');
    }

    /** @test */
    public function agent_dashboard_has_collaborating_list()
    {
        $this->be($this->make->super->user);

        $this->visit(self::URI)
            ->see('id="collab-count-title"')
            ->see('id="collab-count-number"')
            ->see('id="collab-title"')
            ->see('id="collab-no-results"');
    }
}
