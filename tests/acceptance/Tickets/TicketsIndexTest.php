<?php

namespace Aviator\Helpdesk\Tests;

class TicketsIndexTest extends AdminBase
{
    const VERB = 'GET';
    const URI = 'helpdesk/tickets/';

    /** @test */
    public function access_test ()
    {
        $this->noGuests();
    }

    /** @test */
    public function users_can_visit ()
    {
        $this->be($this->make->user);

        $this->visit(self::URI)
            ->assertResponseOk()
            ->see('Helpdesk');
    }

    /** @test */
    public function agents_can_visit ()
    {
        $this->be($this->make->agent->user);

        $this->visit(self::URI)
            ->assertResponseOk()
            ->see('Helpdesk');
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
    public function users_cannot_see_other_tickets ()
    {
        $user = $this->make->user;
        $user2 = $this->make->user;

        $ticket = $this->make->ticket($user);
        $ticket2 = $this->make->ticket($user2);

        $this->be($user);

        $this->visit(self::URI)
            ->assertResponseOk()
            ->see($ticket->content->title())
            ->dontSee($ticket2->content->title());
    }

    /** @test */
    public function agents_can_see_their_assigned_tickets_but_not_other_tickets ()
    {
        $agent1 = $this->make->agent;
        $agent2 = $this->make->agent;

        $unassignedTicket = $this->make->ticket;
        $assignedTicket1 = $this->make->ticket->assignToAgent($agent1);
        $assignedTicket2 = $this->make->ticket->assignToAgent($agent2);

        $this->be($agent1->user);

        $this->visit(self::URI)
            ->assertResponseOk()
            ->see($assignedTicket1->content->title())
            ->dontSee($unassignedTicket->content->title())
            ->dontSee($assignedTicket2->content->title());
    }

    /** @test */
    public function team_leads_can_see_their_assigned_tickets_and_tickets_assigned_to_their_team ()
    {
        $agent1 = $this->make->agent;
        $agent2 = $this->make->agent;
        $team = $this->make->team;

        $unassignedTicket = $this->make->ticket;
        $agent1ticket = $this->make->ticket->assignToAgent($agent1);
        $agent2ticket = $this->make->ticket->assignToAgent($agent2);
        $teamTicket = $this->make->ticket->assignToTeam($team);

        $agent1->makeTeamLeadOf($team);

        $this->be($agent1->user);

        $this->visit(self::URI)
            ->assertResponseOk()
            ->see($agent1ticket->content->title())
            ->see($teamTicket->content->title())
            ->dontSee($unassignedTicket->content->title())
            ->dontSee($agent2ticket->content->title());
    }

    /** @test */
    public function super_users_can_see_all_tickets ()
    {
        $agent1 = $this->make->agent;
        $agent2 = $this->make->agent;
        $team = $this->make->team;
        $super = $this->make->super;

        $agent1->makeTeamLeadOf($team);

        $userTicket = $this->make->ticket;
        $agent1Ticket = $this->make->ticket->assignToAgent($agent1);
        $agent2Ticket = $this->make->ticket->assignToAgent($agent2);
        $teamTicket = $this->make->ticket->assignToTeam($team);

        $this->be($super->user);

        $this->visit(self::URI)
            ->assertResponseOk()
            ->see($agent1Ticket->content->title())
            ->see($teamTicket->content->title())
            ->see($userTicket->content->title())
            ->see($agent2Ticket->content->title());
    }

    /** @test */
    public function for_less_than_25_tickets_the_see_more_button_is_disabled ()
    {
        $user = $this->make->user;
        $this->make->ticket($user);

        $this->be($user);

        $this->visit(self::URI)
            ->assertResponseOk()
            ->see('<a id="open-see-more" class="button is-disabled">No more to show...</a>');
    }

    /** @test */
    public function for_more_than_25_tickets_the_see_more_button_is_enabled ()
    {
        $user = $this->make->user;
        $this->make->tickets(26, $user);

        $this->be($user);

        $this->visit(self::URI)
            ->assertResponseOk()
            ->see('<a id="open-see-more" class="button" href=');
    }
}
