<?php

namespace Aviator\Helpdesk\Tests\Integration\Users\Tickets;

use Aviator\Helpdesk\Tests\TestCase;

class IndexTest extends TestCase
{
    /** @var string */
    protected $url = 'helpdesk/tickets';

    /** @test */
    public function guests_may_not_visit ()
    {
        $response = $this->get($this->url);

        $response->assertStatus(302)
            ->assertRedirect('login');
    }

    /** @test */
    public function agents_may_not_visit ()
    {
        $this->be($this->make->agent->user);

        $response = $this->get($this->url);

        $response->assertStatus(403);
    }

    /** @test */
    public function users_can_visit ()
    {
        $this->be($this->make->user);

        $response = $this->get($this->url);

        $response->assertStatus(200);
    }

    /** @test */
    public function users_only_see_their_own_open_and_closed_tickets ()
    {
        $user = $this->make->user;
        $openTicket = $this->make->ticket($user);
        $closedTicket = $this->make->ticket($user)->close(null, $user);
        $otherOpenTicket = $this->make->ticket;
        $otherClosedTicket = $this->make->ticket->close(null, $user);

        $this->be($user);

        $response = $this->get($this->url);

        $response->data('open')->assertContains($openTicket);
        $response->data('open')->assertDoesntContain($otherOpenTicket);
        $response->data('closed')->assertContains($closedTicket);
        $response->data('closed')->assertDoesntContain($otherClosedTicket);
    }

    /** @test */
    public function only_the_header_tickets_tab_is_enabled ()
    {
        $this->withoutErrorHandling();
        $user = $this->make->user;

        $this->be($user);
        $response = $this->get($this->url);

        $response->assertSee('id="header-tab-tickets-active"');
        $response->assertDontSee('id="header-tab-dashboard-active"');
        $response->assertDontSee('id="header-tab-admin-active"');
    }

    /** @test */
    public function for_less_than_24_tickets_the_see_more_button_is_disabled ()
    {
        $user = $this->make->user;
        $this->make->ticket($user);

        $this->be($user);

        $response = $this->get($this->url);

        $response->assertSee('<a id="open-see-more" class="button is-disabled">No more to show...</a>');
    }

    /** @test */
    public function for_more_than_24_tickets_the_see_more_button_is_enabled ()
    {
        $user = $this->make->user;
        $this->make->tickets(25, $user);

        $this->be($user);
        $response = $this->get($this->url);

        $response->assertSee('<a id="open-see-more" class="button" href=');
    }
}
