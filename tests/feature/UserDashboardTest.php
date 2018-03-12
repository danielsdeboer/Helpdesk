<?php

namespace Aviator\Helpdesk\Tests;

class UserDashboardTest extends BKTestCase
{
    const URI = 'helpdesk/dashboard/user';

    /** @test */
    public function a_guest_cannot_visit_the_user_dashboard()
    {
        $this->get(self::URI);

        $this->assertResponseStatus(302);
        $this->assertRedirectedTo('login');
    }

    /** @test */
    public function a_user_can_visit_their_dashboard()
    {
        $this->be($this->make->user);
        $this->visit(self::URI);

        $this->assertResponseOk();
    }

    /** @test */
    public function a_user_can_see_their_dashboard()
    {
        $this->be($this->make->user);

        $this->visit(self::URI)
            ->see('Helpdesk')
            ->see('Overdue')
            ->see('Open')
            ->see('Nothing to see here!')
            ->dontSee('id="header-tab-admin"');
    }
}
