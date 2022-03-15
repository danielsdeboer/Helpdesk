<?php

namespace Aviator\Helpdesk\Tests\Acceptance\Dashboard\Acceptance\Tickets\Acceptance\Acceptance\Acceptance\Acceptance\Acceptance\Acceptance\Acceptance\Acceptance\Acceptance\Tickets\Integration\Tickets\Open;

use Aviator\Helpdesk\Tests\TestCase;
use Carbon\Carbon;

class IndexTest extends TestCase
{
    /** @var string */
    protected $url = 'helpdesk/tickets/open';

    /** @test */
    public function guests_are_redirected_to_login ()
    {
        $response = $this->get($this->url);

        $response->assertStatus(302)
            ->assertRedirect('login');
    }

    /** @test */
    public function users_only_see_their_own_open_tickets ()
    {
        $user = $this->make->user;
        $ticket1 = $this->make->ticket($user);
        $ticket2 = $this->make->ticket($user)->close(null, $user);
        $ticket3 = $this->make->ticket;
        $ticket4 = $this->make->ticket->close(null, $user);

        $this->be($user);

        $response = $this->get($this->url);

        $response->assertStatus(200);
        $response->data('open')->assertContains($ticket1);
        $response->data('open')->assertNotContains($ticket2);
        $response->data('open')->assertNotContains($ticket3);
        $response->data('open')->assertNotContains($ticket4);
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
        $response->data('open')->assertContains($ticket1);
        $response->data('open')->assertNotContains($ticket2);
        $response->data('open')->assertNotContains($ticket3);
        $response->data('open')->assertNotContains($ticket4);
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
        $tickets = $response->data('open');

        $tickets->assertNotContains($ticket1);
        $tickets->assertNotContains($ticket2);
        $tickets->assertNotContains($ticket3);
        $tickets->assertNotContains($ticket4);

        $tickets->assertContains($ticket5);
        $tickets->assertNotContains($ticket6);
        $tickets->assertContains($ticket7);
        $tickets->assertNotContains($ticket8);
    }

    /** @test */
    public function results_are_paginated_when_displaying_more_than_24_tickets ()
    {
        $this->withoutErrorHandling();
        $user = $this->make->user;
        $ticket = $this->make->ticket($user);

        $this->be($user);
        $response = $this->get($this->url);

        // We have no pagination here since there are too few results.
        $response->assertStatus(200);
        $response->assertDontSee('nav class="pagination"');

        $this->make->tickets(24, $user);

        $response = $this->get($this->url);

        // We have pagination due to the number of results.
        $response->assertStatus(200);
        $response->assertSee('ul class="pagination-list"', false);
    }

    /** @test */
    public function results_are_ordered_by_latest_first ()
    {
        $user = $this->make->user;
        $ticket1 = $this->make->ticket($user);
        $ticket2 = $this->make->ticket($user, '10 minutes ago');
        $ticket3 = $this->make->ticket($user, '10 years ago');

        $ticket1->created_at = Carbon::parse('2 years ago');
        $ticket1->save();
        $ticket2->created_at = Carbon::parse('yesterday');
        $ticket2->save();
        $ticket3->created_at = Carbon::now();
        $ticket3->save();

        $this->be($user);
        $response = $this->get($this->url);

        $response->assertStatus(200);
        $this->assertSame($ticket3->id, $response->data('open')[0]->id);
        $this->assertSame($ticket2->id, $response->data('open')[1]->id);
        $this->assertSame($ticket1->id, $response->data('open')[2]->id);
    }

    /** @test */
    public function users_see_the_user_table_sorted_by_newest_first ()
    {
        $user = $this->make->user;
        $agent = $this->make->agent;
        $ticket1 = $this->make->ticket($user)
            ->assignToAgent($agent);
        $ticket2 = $this->make->ticket($user, '10 minutes ago');

        $this->be($user);
        $response = $this->get($this->url);

        $response->assertSuccessful();
        $response->assertSeeInOrder([
            '<td id="row-1-title">',
            $ticket1->content->title(),
            '<td id="row-1-created">',
            $ticket1->created_at->format('Y-m-d'),
            '<td id="row-1-assignee">',
            $ticket1->assignment->assignee->user->name,
        ], false);
        $response->assertSeeInOrder([
            '<td id="row-2-title">',
            $ticket2->content->title(),
            '<td id="row-2-created">',
            $ticket2->created_at->format('Y-m-d'),
            '<td id="row-2-assignee">',
            'No One Yet',
        ], false);
    }

    /** @test */
    public function agents_see_the_agent_table_sorted_by_newest_first ()
    {
        $user1 = $this->make->user;
        $user2 = $this->make->user;
        $agent = $this->make->agent;
        $ticket1 = $this->make->ticket($user1)->assignToAgent($agent);
        $ticket2 = $this->make->ticket($user2, '1 year ago')
            ->assignToAgent($agent);

        $user2->delete();

        $this->be($agent->user);
        $response = $this->get($this->url);

        $response->assertSuccessful();
        $response->assertSeeInOrder([
            '<td id="row-1-title">',
            $ticket1->content->title(),
            '<td id="row-1-user">',
            $ticket1->user->name,
            '<td id="row-1-created">',
            $ticket1->created_at->format('Y-m-d'),
        ], false);
        $response->assertSeeInOrder([
            '<td id="row-2-title">',
            $ticket2->content->title(),
            '<td id="row-2-user">',
            '(Deleted User)',
            '<td id="row-2-created">',
            $ticket2->created_at->format('Y-m-d'),
        ], false);
    }

    /** @test */
    public function only_ignored_users_can_see_their_open_tickets ()
    {
        $user = $this->make->user;
        $ignoredUser = $this->make->user;
        $super = $this->make->super;

        $this->addIgnoredUser([$ignoredUser->email]);

        $ignoredOpenTicket = $this->make->ticket($ignoredUser);

        $response = $this->actingAs($ignoredUser)->get($this->url);
        $htmlString = $response->getContent();
        $response->assertSee('<td id="row-1-title">', false);

        $response = $this->actingAs($super->user)->get($this->url);
        $htmlString = $response->getContent();
        $response->assertDontSee('<td id="row-1-title">', false);
    }
}
