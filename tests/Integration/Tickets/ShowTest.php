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
    public function it_shows_tickets_status_tags ()
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
    public function users_dont_see_agent_actions ()
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

    /** @test */
    public function agents_dont_see_team_lead_actions ()
    {
        $agentActions = ['reply', 'note', 'close', 'collab'];
        $leadActions = ['assign'];

        $user = $this->make->user;
        $ticket = $this->make->ticket($user);
        $agent = $this->make->agent->assign($ticket);

        $this->be($agent->user);
        $response = $this->get($this->url($ticket->id));

        $response->assertSuccessful();

        foreach ($agentActions as $action) {
            $response->assertSee('id="toolbar-action-' . $action . '"');
        }

        foreach ($leadActions as $action) {
            $response->assertDontSee('id="toolbar-action-' . $action . '"');
        }
    }

    /** @test */
    public function team_leads_see_all_actions ()
    {
        $openActions = ['reply', 'note', 'close', 'assign'];

        $assignedActions = ['reassign', 'collab'];

        $closedActions = ['open'];

        $user = $this->make->user;
        $ticket = $this->make->ticket($user);
        $agent = $this->make->agent;
        $team = $this->make->team->assign($ticket)->addLead($agent);

        $this->be($agent->user);
        $response = $this->get($this->url($ticket->id));

        $response->assertSuccessful();

        foreach ($openActions as $action) {
            $response->assertSee('id="toolbar-action-' . $action . '"');
        }

        $this->make->agent->assign($ticket);
        $response = $this->get($this->url($ticket->id));

        foreach ($assignedActions as $action) {
            $response->assertSee('id="toolbar-action-' . $action . '"');
        }

        $ticket->close(null, $agent);
        $response = $this->get($this->url($ticket->id));

        foreach ($closedActions as $action) {
            $response->assertSee('id="toolbar-action-' . $action . '"');
        }
    }

    /** @test */
    public function agents_are_listed_alphabetically ()
    {
        $agent1 = $this->make->agentNamed('zzz');
        $agent2 = $this->make->agentNamed('aaa');
        $agent3 = $this->make->agentNamed('ggg');
        $agent4 = $this->make->agentNamed('yyy');

        $ticket = $this->make->ticket;
        $this->make->team->addLead($agent1)->assign($ticket)->addMembers([
            $agent2,
            $agent3,
            $agent4,
        ]);

        $this->be($agent1->user);
        $response = $this->get($this->url($ticket->id));

        $sorted = ['aaa', 'ggg', 'yyy', 'zzz'];
        $count = 0;
        $response->data('agents')->each(function ($item) use ($sorted, &$count) {
            $this->assertSame($sorted[$count], $item->user_name);
            $count++;
        });
    }

    /** @test */
    public function users_dont_see_private_actions ()
    {
        $user = $this->make->user;
        $agent = $this->make->agent;
        $ticket = $this->make->ticket($user)->assignToAgent($agent, null, false);

        $this->be($user);

        $response = $this->get($this->url($ticket->id));

        $response->assertSuccessful();
        $response->assertSee('id="action-opened"');
        $response->assertDontSee('id="action-assigned"');
    }

    /** @test */
    public function agents_see_private_actions ()
    {
        $user = $this->make->user;
        $agent = $this->make->agent;
        $ticket = $this->make->ticket($user)->assignToAgent($agent, null, false);

        $this->be($agent->user);

        $response = $this->get($this->url($ticket->id));

        $response->assertSuccessful();
        $response->assertSee('id="action-opened"');
        $response->assertSee('id="action-assigned"');
    }
}
