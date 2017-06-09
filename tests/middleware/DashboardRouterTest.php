<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Agent;


class DashboardRouterTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    /**
     * @group middle
     * @group middle.dash
     * @test
     */
    public function it_routes_guests_to_the_login_page()
    {
        $this->route('GET', 'helpdesk.dashboard.router');

        $this->assertResponseStatus(302);
        $this->assertRedirectedTo('login');
    }

    /**
     * @group middle
     * @group middle.dash
     * @test
     */
    public function it_routes_users_to_the_public_dashboard()
    {
        $this->be(factory(User::class)->create());

        $this->route('GET', 'helpdesk.dashboard.router');

        $this->assertResponseStatus(302);
        $this->assertRedirectedToRoute('helpdesk.dashboard.user');
    }

    /**
     * @group middle
     * @group middle.dash
     * @test
     */
    public function it_routes_agents_to_the_agent_dashboard()
    {
        $this->be(factory(Agent::class)->create()->user);

        $this->route('GET', 'helpdesk.dashboard.router');

        $this->assertResponseStatus(302);
        $this->assertRedirectedToRoute('helpdesk.dashboard.agent');
    }

    /**
     * @group middle
     * @group middle.dash
     * @test
     */
    public function it_routes_supervisors_to_the_supervisor_dashboard()
    {
        $this->be(factory(User::class)->create([
            'email' => config('helpdesk.supervisor.email'),
        ]));

        $this->route('GET', 'helpdesk.dashboard.router');

        $this->assertResponseStatus(302);
        $this->assertRedirectedToRoute('helpdesk.dashboard.supervisor');
    }
}
