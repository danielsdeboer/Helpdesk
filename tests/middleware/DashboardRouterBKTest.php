<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Agent;

class DashboardRouterBKTest extends BKTestCase
{
    /** @var string */
    protected $routerUri;

    public function setUp()
    {
        parent::setUp();

        $this->routerUri = route('helpdesk.dashboard.router');
    }

    /**
     * @test
     */
    public function it_routes_guests_to_the_login_page()
    {
        $this->get($this->routerUri);

        $this->assertResponseStatus(302);
        $this->assertRedirectedTo('login');
    }

    /**
     * @test
     */
    public function it_routes_users_to_the_public_dashboard()
    {
        $this->be(factory(User::class)->create());

        $this->get($this->routerUri);

        $this->assertResponseStatus(302);
        $this->assertRedirectedToRoute('helpdesk.dashboard.user');
    }

    /**
     * @test
     */
    public function it_routes_agents_to_the_agent_dashboard()
    {
        $this->be(factory(Agent::class)->create()->user);

        $this->get($this->routerUri);

        $this->assertResponseStatus(302);
        $this->assertRedirectedToRoute('helpdesk.dashboard.agent');
    }

    /**
     * @test
     */
    public function it_routes_supervisors_to_the_supervisor_dashboard()
    {
        $this->be(
            $this->make->super->user
        );

        $this->get($this->routerUri);

        $this->assertResponseStatus(302);
        $this->assertRedirectedToRoute('helpdesk.dashboard.supervisor');
    }
}
