<?php

namespace Aviator\Helpdesk\Tests\Integration\Agents\Tickets;

use Aviator\Helpdesk\Tests\TestCase;

class ShowTest extends TestCase
{
    /** @var string */
    protected $url = 'helpdesk/agents/tickets/1';

    /** @test */
    public function guests_may_not_visit ()
    {
        $response = $this->get($this->url);

        $response->assertStatus(302)
            ->assertRedirect('login');
    }

    /** @test */
    public function agents_may_visit ()
    {
        $this->be(
            tm('agent')->user
        );

        $response = $this->get($this->url);

        $response->assertStatus(200);
    }

    /** @test */
    public function users_may_not_visit ()
    {
        $this->be(
            tm('user')
        );

        $response = $this->get($this->url);

        $response->assertStatus(403);
    }

    /** @test */
    public function agents_only_see_tickets_assigned_to_them ()
    {
        $agent = tm('agent');

        $ticket1 = tm('ticket')->assignToAgent($agent);
        $ticket2 = tm('ticket')->assignToAgent($agent)->close(null, $agent);
        $ticket3 = tm('ticket');
        $ticket4 = tm('ticket')->close(null, $agent);

        $this->be($agent->user);

        $response = $this->get($this->url);

        $response->data('open')->assertContains($ticket1);
        $response->data('closed')->assertContains($ticket2);

        $response->data('open')->assertDoesntContain($ticket3);
        $response->data('closed')->assertDoesntContain($ticket4);
    }

    /** @test */
    public function team_leads_see_tickets_assigned_to_them_or_their_team ()
    {
        $agent = tm('agent');
        $team = tm('team');
        $agent->makeTeamLeadOf($team);

        $ticket1 = tm('ticket')->assignToAgent($agent);
        $ticket2 = tm('ticket')->assignToAgent($agent)->close(null, $agent);
        $ticket3 = tm('ticket');
        $ticket4 = tm('ticket')->close(null, $agent);
        $ticket5 = tm('ticket')->assignToTeam($team);
        $ticket6 = tm('ticket')->assignToTeam($team)->close(null, $agent);

        $this->be($agent->user);

        $response = $this->get($this->url);

        $response->data('open')->assertContains($ticket1);
        $response->data('closed')->assertContains($ticket2);

        $response->data('open')->assertDoesntContain($ticket3);
        $response->data('closed')->assertDoesntContain($ticket4);

        $response->data('open')->assertContains($ticket5);
        $response->data('closed')->assertContains($ticket6);
    }

    /** @test */
    public function supers_see_all_tickets ()
    {
        $super = tm('super');
        $agent = tm('agent');
        $team = tm('team');

        $ticket1 = tm('ticket')->assignToAgent($agent);
        $ticket2 = tm('ticket')->assignToAgent($agent)->close(null, $super);
        $ticket3 = tm('ticket');
        $ticket4 = tm('ticket')->close(null, $super);
        $ticket5 = tm('ticket')->assignToTeam($team);
        $ticket6 = tm('ticket')->assignToTeam($team)->close(null, $super);

        $this->be($super->user);

        $response = $this->get($this->url);

        $response->data('open')->assertContains($ticket1);
        $response->data('closed')->assertContains($ticket2);
        $response->data('open')->assertContains($ticket3);
        $response->data('closed')->assertContains($ticket4);
        $response->data('open')->assertContains($ticket5);
        $response->data('closed')->assertContains($ticket6);
    }
}
