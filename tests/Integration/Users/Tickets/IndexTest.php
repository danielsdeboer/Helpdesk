<?php

namespace Aviator\Helpdesk\Tests;

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
    public function users_sees_their_own_open_and_closed_tickets ()
    {
        $user = $this->make->user;
        $openTicket = $this->make->ticket($user);
        $closedTicket = $this->make->ticket($user)->close(null, $user);
        $notTheirOpenTicket = $this->make->ticket;
        $notTheirClosedTicket = $this->make->ticket->close(null, $user);

        $this->be($user);

        $response = $this->get($this->url);

        $response->data('open')->assertContains($openTicket);
        $response->data('open')->assertDoesntContain($notTheirOpenTicket);
        $response->data('closed')->assertContains($closedTicket);
        $response->data('closed')->assertDoesntContain($notTheirClosedTicket);
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
