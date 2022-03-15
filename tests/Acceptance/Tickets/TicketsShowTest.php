<?php

namespace Aviator\Helpdesk\Tests\Acceptance\Tickets;

use Aviator\Helpdesk\Tests\AdminBase;

class TicketsShowTest extends AdminBase
{
    const VERB = 'GET';
    const URI = 'helpdesk/tickets/1';

    /** @test */
    public function access_test()
    {
        $this->noGuests();
    }

    /** @test */
    public function users_can_see_their_own_tickets ()
    {
        $user = $this->make->user;
        $ticket = $this->make->ticket($user);

        $this->be($user);

        $this->visit(self::URI)
            ->assertResponseOk()
            ->see($ticket->content->title());
    }

    /** @test */
    public function users_cant_see_other_tickets ()
    {
        $user = $this->make->user;
        $this->make->ticket;

        $this->be($user);
        $this->get(self::URI);

        $this->assertResponseStatus(404);
    }

    /** @test */
    public function agents_can_see_tickets_assigned_to_them ()
    {
        $agent = $this->make->agent;
        $ticket = $this->make->ticket->assignToAgent($agent);

        $this->be($agent->user);

        $this->visit(self::URI)
            ->assertResponseOk()
            ->see($ticket->content->title());
    }

    /** @test */
    public function agents_cant_see_tickets_not_assigned_to_them ()
    {
        $agent = $this->make->agent;
        $this->make->ticket;

        $this->be($agent->user);
        $this->get(self::URI);

        $this->assertResponseStatus(404);
    }

    /** @test */
    public function agents_cant_see_tickets_assigned_to_another_agent ()
    {
        $agent = $this->make->agent;
        $agent2 = $this->make->agent;
        $this->make->ticket->assignToAgent($agent2);

        $this->be($agent->user);
        $this->get(self::URI);

        $this->assertResponseStatus(404);
    }

    /** @test */
    public function agents_cant_see_tickets_assigned_to_their_team_unless_it_is_assigned_to_them ()
    {
        $agent = $this->make->agent;
        $team = $this->make->team;
        $this->make->ticket->assignToTeam($team);

        $agent->addToTeam($team);
        $this->be($agent->user);
        $this->get(self::URI);

        $this->assertResponseStatus(404);
    }

    /** @test */
    public function team_leads_can_see_tickets_assigned_to_their_team ()
    {
        $agent = $this->make->agent;
        $team = $this->make->team;
        $this->make->ticket->assignToTeam($team);

        $agent->makeTeamLeadOf($team);

        $this->be($agent->user);
        $this->get(self::URI);

        $this->assertResponseOk();
    }

    /** @test */
    public function super_can_see_everything ()
    {
        $agent = $this->make->super;
        $agent2 = $this->make->agent;
        $ticket = $this->make->ticket;

        $this->be($agent->user);
        $this->get(self::URI);

        $this->assertResponseOk();

        $ticket->assignToAgent($agent2);

        $this->be($agent->user);
        $this->get(self::URI);

        $this->assertResponseOk();
    }
}
