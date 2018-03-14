<?php

namespace Aviator\Helpdesk\Tests\Integration\Users\Tickets;

use Aviator\Helpdesk\Tests\TestCase;

class ShowTest extends TestCase
{
    /** @var string */
    protected $url = 'helpdesk/tickets/';

    /**
     * @param null $id
     * @return string
     */
    protected function url ($id = null) : string
    {
        return $this->url . ($id ?: 1);
    }

    /** @test */
    public function guests_may_not_visit ()
    {
        $response = $this->get($this->url());

        $response->assertStatus(302)
            ->assertRedirect('login');
    }

    /** @test */
    public function agents_are_redirected_to_their_tickets_index ()
    {
        $this->be($this->make->agent->user);

        $response = $this->get($this->url());

        $response->assertStatus(302);
        $response->assertRedirect(route('helpdesk.agents.tickets.index'));
    }

    /** @test */
    public function users_can_visit ()
    {
        $user = $this->make->user;
        $ticket = $this->make->ticket($user);

        $this->be($user);

        $response = $this->get(
            $this->url($ticket->id)
        );

        $response->assertStatus(200);
    }

    /** @test */
    public function users_can_only_visit_their_own_tickets ()
    {
        $user = $this->make->user;
        $ticket1 = $this->make->ticket;
        $ticket2 = $this->make->ticket($user);

        $this->be($user);

        $response1 = $this->get($this->url($ticket1->id));
        $response1->assertStatus(404);

        $response2 = $this->get($this->url($ticket2->id));
        $response2->assertStatus(200);
    }

    /** @test */
    public function it_shows_the_header_with_tickets_tab_active ()
    {
        $user = $this->make->user;
        $ticket = $this->make->ticket($user);

        $this->be($user);
        $response = $this->get($this->url($ticket->id));

        $response->assertActiveHeaderTab('tickets');
    }

    /** @test */
    public function it_shows_tickets_statuses ()
    {
        $user = $this->make->user;
        $ticket = $this->make->ticket($user);

        $this->be($user);

        $response = $this->get($this->url($ticket->id));
        $response->assertSee('id="status-tag-not-assigned"');

        $ticket->assignToTeam($this->make->team);

        $response = $this->get($this->url($ticket->id));
        $response->assertSee('id="status-tag-assigned-to-team"');

        $ticket->assignToAgent($this->make->agent);

        $response = $this->get($this->url($ticket->id));
        $response->assertSee('id="status-tag-assigned"');

        $ticket->close(null, $user);

        $response = $this->get($this->url($ticket->id));
        $response->assertSee('id="status-tag-closed"');
    }

    /** @test */
    public function when_a_ticket_is_open_a_user_may_close_or_reply ()
    {
        $user = $this->make->user;
        $ticket = $this->make->ticket($user);

        $this->be($user);

        $response = $this->get($this->url($ticket->id));

        $response->assertSee('id="toolbar-action-close"');
        $response->assertSee('id="toolbar-action-reply"');
        $response->assertDontSee('id="toolbar-action-open"');

        $ticket->close('note', $user);

        $response = $this->get($this->url($ticket->id));

        $response->assertDontSee('id="toolbar-action-close"');
        $response->assertDontSee('id="toolbar-action-reply"');
        $response->assertSee('id="toolbar-action-open"');
    }

    /** @test */
    public function a_user_does_not_see_agent_actions ()
    {
        $adminActions = ['assign', 'note', 'collab'];

        $user = $this->make->user;
        $ticket = $this->make->ticket($user);

        $this->be($user);

        $response = $this->get($this->url($ticket->id));

        foreach ($adminActions as $action) {
            $response->assertDontSee('id="toolbar-action-' . $action . '"');
        }
    }
}