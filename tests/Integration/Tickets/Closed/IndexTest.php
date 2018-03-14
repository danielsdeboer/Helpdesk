<?php

namespace Aviator\Helpdesk\Tests\Integration\Tickets\Closed;

use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Tests\TestCase;
use Carbon\Carbon;

class IndexTest extends TestCase
{
    /** @var string */
    protected $url = 'helpdesk/tickets/closed';

    /** @test */
    public function guests_are_redirected_to_login ()
    {
        $response = $this->get($this->url);

        $response->assertStatus(302)
            ->assertRedirect('login');
    }

    /** @test */
    public function users_only_see_their_own_closed_tickets ()
    {
        $this->withoutErrorHandling();
        $user = $this->make->user;
        $ticket1 = $this->make->ticket($user);
        $ticket2 = $this->make->ticket($user)->close(null, $user);
        $ticket3 = $this->make->ticket;
        $ticket4 = $this->make->ticket->close(null, $user);

        $this->be($user);

        $response = $this->get($this->url);

        $response->assertStatus(200);
        $response->data('closed')->assertNotContains($ticket1);
        $response->data('closed')->assertContains($ticket2);
        $response->data('closed')->assertNotContains($ticket3);
        $response->data('closed')->assertNotContains($ticket4);
    }

    /** @test */
    public function agents_see_tickets_assigned_to_them ()
    {
        $agent = $this->make->agent;
        $ticket1 = $this->make->ticket->assignToAgent($agent);
        $ticket2 = $this->make->ticket->assignToAgent($agent)->close('note', $agent);
        $ticket3 = $this->make->ticket;
        $ticket4 = $this->make->ticket->close('note', $agent);

        $this->be($agent->user);

        $response = $this->get($this->url);

        $response->assertStatus(200);
        $response->data('closed')->assertNotContains($ticket1);
        $response->data('closed')->assertContains($ticket2);
        $response->data('closed')->assertNotContains($ticket3);
        $response->data('closed')->assertNotContains($ticket4);
    }

    /** @test */
    public function team_leads_see_all_ticket_assigned_to_their_team()
    {
        $agent = $this->make->agent;
        $lead = $this->make->agent;
        $team1 = $this->make->team;
        $team2 = $this->make->team;

        $lead->makeTeamLeadOf($team1);
        $agent->addToTeam($team1);

        // Tickets assigned to no one
        $ticket1 = $this->make->ticket;
        $ticket2 = $this->make->ticket->close(null, $agent);

        // Tickets assigned to another team
        $ticket3 = $this->make->ticket->assignToTeam($team2);
        $ticket4 = $this->make->ticket->assignToTeam($team2)->close(null, $agent);

        // Tickets assigned to the team
        $ticket5 = $this->make->ticket->assignToTeam($team1);
        $ticket6 = $this->make->ticket->assignToTeam($team1)->close(null, $agent);

        // Tickets assigned to a team and then to a team member
        $ticket7 = $this->make->ticket
            ->assignToTeam($team1)
            ->assignToAgent($agent);
        $ticket8 = $this->make->ticket
            ->assignToTeam($team1)
            ->assignToAgent($agent)
            ->close(null, $agent);

        $this->be($lead->user);
        $response = $this->get($this->url);

        $response->assertStatus(200);
        $tickets = $response->data('closed');

        $tickets->assertNotContains($ticket1);
        $tickets->assertNotContains($ticket2);
        $tickets->assertNotContains($ticket3);
        $tickets->assertNotContains($ticket4);

        $tickets->assertNotContains($ticket5);
        $tickets->assertContains($ticket6);
        $tickets->assertNotContains($ticket7);
        $tickets->assertContains($ticket8);
    }

    /** @test */
    public function results_are_paginated_when_displaying_more_than_24_tickets ()
    {
        $this->withoutErrorHandling();
        $user = $this->make->user;
        $ticket = $this->make->ticket($user)->close(null, $user);

        $this->be($user);
        $response = $this->get($this->url);

        // We have no pagination here since there are too few results.
        $response->assertStatus(200);
        $response->assertDontSee('nav class="pagination"');

        $this->make
            ->tickets(24, $user)
            ->each(function (Ticket $ticket) use ($user) {
                $ticket->close(null, $user);
            });

        $response = $this->get($this->url);

        // We have pagination due to the number of results.
        $response->assertStatus(200);
        $response->assertSee('nav class="pagination"');
    }

    /** @test */
    public function results_are_ordered_by_latest_first ()
    {
        $user = $this->make->user;
        $ticket1 = $this->make->ticket($user)->close(null, $user);
        $ticket2 = $this->make->ticket($user)->close(null, $user);
        $ticket3 = $this->make->ticket($user)->close(null, $user);

        $ticket1->created_at = Carbon::parse('2 years ago');
        $ticket1->save();
        $ticket2->created_at = Carbon::parse('yesterday');
        $ticket2->save();
        $ticket3->created_at = Carbon::now();
        $ticket3->save();

        $this->be($user);
        $response = $this->get($this->url);

        $response->assertStatus(200);
        $this->assertSame($ticket3->id, $response->data('closed')[0]->id);
        $this->assertSame($ticket2->id, $response->data('closed')[1]->id);
        $this->assertSame($ticket1->id, $response->data('closed')[2]->id);
    }
}
