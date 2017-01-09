<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Pool;
use Aviator\Helpdesk\Models\Ticket;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UserDashboardTest extends TestCase
{
    /**
     * @group feature
     * @group dashboard
     * @group user
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
     * @group dashboard
     * @group user
     * @test
     */
    public function an_user_can_visit_their_dashboard()
    {
        $this->be(factory(User::class)->create());

        $response = $this->call('GET', 'helpdesk/dashboard/user');

        $this->assertResponseOk();
    }

    /**
     * @group feature
     * @group dashboard
     * @group user
     * @test
    */
    public function the_dashboard_returns_the_correct_json_structure()
    {
        $this->be(factory(User::class)->create());

        $response = $this->call('get', 'helpdesk/dashboard/user');

        $this->assertResponseOk();
        $this->seeJsonStructure([
            'user',
            'overdue',
        ]);
    }
}