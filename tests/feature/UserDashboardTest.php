<?php

namespace Aviator\Helpdesk\Tests;



class UserDashboardTest extends TestCase
{
    /**
     * @group feature
     * @group feature.dash
     * @group feature.dash.user
     * @test
     */
    public function a_guest_cannot_visit_the_user_dashboard()
    {
        $response = $this->call('GET', 'helpdesk/dashboard/user');

        $this->assertResponseStatus(302);
        $this->assertRedirectedTo('login');
    }

    /**
     * @group feature
     * @group feature.dash
     * @group feature.dash.user
     * @test
     */
    public function a_user_can_visit_their_dashboard()
    {
        $this->be(factory(User::class)->create());

        $response = $this->call('GET', 'helpdesk/dashboard/user');

        $this->assertResponseOk();
    }

    /**
     * @group feature
     * @group feature.dash
     * @group feature.dash.user
     * @test
     */
    public function a_user_can_see_their_dashboard()
    {
        $this->be(factory(User::class)->create());

        $this->visit('helpdesk/dashboard/user')
            ->see('Helpdesk')
            ->see('Overdue')
            ->see('Open')
            ->see('Nothing to see here!')
            ->dontSee('id="header-tab-admin"');
    }
}
