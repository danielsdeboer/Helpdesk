<?php

namespace Aviator\Helpdesk\Tests\Middleware;

use Aviator\Helpdesk\Tests\TestCase;

class TicketsRedirectorTest extends TestCase
{
    /** @var string */
    protected $uri;

    public function setUp ()
    {
        parent::setUp();

        $this->uri = route('helpdesk.tickets.redirect');
    }

    /** @test */
    public function it_redirects_guests_to_the_login_page ()
    {
        $response = $this->get($this->uri);

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }

    /** @test */
    public function it_redirects_agents_to_their_tickets_index ()
    {
        $this->be($this->make->agent->user);

        $response = $this->get($this->uri);

        $response->assertStatus(302);
        $response->assertRedirect(route('helpdesk.agents.tickets.index'));
    }

    /** @test */
    public function it_allows_users_to_continue ()
    {
        $this->be($this->make->user);

        $response = $this->get($this->uri);

        $response->assertStatus(200);
    }
}
