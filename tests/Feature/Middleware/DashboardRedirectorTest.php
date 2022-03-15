<?php

namespace Aviator\Helpdesk\Tests\Feature\Middleware;

use Aviator\Helpdesk\Tests\TestCase;

class DashboardRedirectorTest extends TestCase
{
    /** @var string */
    protected $url;

    /*
     * Set the url with the route name.
     */
    public function setUp (): void
    {
        parent::setUp();

        $this->url = route('helpdesk.dashboard.router');
    }

    /** @test */
    public function it_routes_guests_to_the_login_page ()
    {
        $response = $this->get($this->url);

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }

    /** @test */
    public function it_routes_users_to_the_public_dashboard ()
    {
        $this->be($this->make->user);

        $response = $this->get($this->url);

        $response->assertStatus(302);
        $response->assertRedirect(route('helpdesk.dashboard.user'));
    }

    /** @test */
    public function it_routes_agents_to_the_agent_dashboard ()
    {
        $this->be($this->make->agent->user);

        $response = $this->get($this->url);

        $response->assertStatus(302);
        $response->assertRedirect(route('helpdesk.dashboard.agent'));
    }

    /** @test */
    public function it_routes_supervisors_to_the_supervisor_dashboard ()
    {
        $this->be($this->make->super->user);

        $response = $this->get($this->url);

        $response->assertStatus(302);
        $response->assertRedirect(route('helpdesk.dashboard.supervisor'));
    }
}
