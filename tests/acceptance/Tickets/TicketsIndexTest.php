<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Pool;
use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Ticket;

class TicketsIndexTest extends AdminBase
{
    const VERB = 'GET';
    const URI = 'helpdesk/tickets/';

    /**
     * @group acc
     * @group acc.ticket
     * @group acc.ticket.index
     * @test
     */
    public function access_test()
    {
        $this->noGuests();
    }

    /**
     * @group acc
     * @group acc.ticket
     * @group acc.ticket.index
     * @test
     */
    public function users_can_visit()
    {
        $this->be($this->makeUser());

        $this->visit(self::URI)
            ->assertResponseOk()
            ->see('Helpdesk');
    }

    /**
     * @group acc
     * @group acc.ticket
     * @group acc.ticket.index
     * @test
     */
    public function agents_can_visit()
    {
        $this->be($this->makeAgent()->user);

        $this->visit(self::URI)
            ->assertResponseOk()
            ->see('Helpdesk');
    }

    /**
     * @group acc
     * @group acc.ticket
     * @group acc.ticket.index
     * @test
     */
    public function users_can_see_their_own_tickets()
    {
        $user = $this->makeUser();

        $ticket = factory(Ticket::class)->create([
            'user_id' => $user->id,
        ]);

        $this->be($user);

        $this->visit(self::URI)
            ->assertResponseOk()
            ->see($ticket->content->title());
    }

    /**
     * @group acc
     * @group acc.ticket
     * @group acc.ticket.index
     * @test
     */
    public function users_cannot_see_other_tickets()
    {
        $user = $this->makeUser();
        $user2 = $this->makeUser();

        $ticket = factory(Ticket::class)->create([
            'user_id' => $user->id,
        ]);

        $ticket2 = factory(Ticket::class)->create([
            'user_id' => $user2->id,
        ]);

        $this->be($user);

        $this->visit(self::URI)
            ->assertResponseOk()
            ->see($ticket->content->title())
            ->dontSee($ticket2->content->title());
    }

    /**
     * @group acc
     * @group acc.ticket
     * @group acc.ticket.index
     * @test
     */
    public function agents_can_see_their_assigned_tickets_but_not_other_tickets()
    {
        $user = $this->makeUser();
        $agent = $this->makeAgent();
        $agent2 = $this->makeAgent();

        $userTicket = factory(Ticket::class)->create([
            'user_id' => $user->id,
        ]);

        $agentTicket = factory(Ticket::class)->create()->assignToAgent($agent);

        $agent2Ticket = factory(Ticket::class)->create()->assignToAgent($agent2);

        $this->be($agent->user);

        $this->visit(self::URI)
            ->assertResponseOk()
            ->see($agentTicket->content->title())
            ->dontSee($userTicket->content->title())
            ->dontSee($agent2Ticket->content->title());
    }

    /**
     * @group acc
     * @group acc.ticket
     * @group acc.ticket.index
     * @test
     */
    public function team_leads_can_see_their_assigned_tickets_and_tickets_assigned_to_their_team()
    {
        $user = $this->makeUser();
        $agent2 = $this->makeAgent();
        $agent = $this->makeAgent();
        $team = factory(Pool::class)->create();

        $agent->makeTeamLeadOf($team);

        $userTicket = factory(Ticket::class)->create([
            'user_id' => $user->id,
        ]);
        $agentTicket = factory(Ticket::class)->create()->assignToAgent($agent);
        $agent2Ticket = factory(Ticket::class)->create()->assignToAgent($agent2);
        $teamTicket = factory(Ticket::class)->create()->assignToPool($team);

        $this->be($agent->user);

        $this->visit(self::URI)
            ->assertResponseOk()
            ->see($agentTicket->content->title())
            ->see($teamTicket->content->title())
            ->dontSee($userTicket->content->title())
            ->dontSee($agent2Ticket->content->title());
    }

    /**
     * @group acc
     * @group acc.ticket
     * @group acc.ticket.index
     * @test
     */
    public function super_users_can_see_all_tickets()
    {
        $user = $this->makeUser();
        $agent2 = $this->makeAgent();
        $agent = $this->makeAgent();
        $team = factory(Pool::class)->create();
        $super = factory(Agent::class)->states('isSuper')->create();

        $agent->makeTeamLeadOf($team);

        $userTicket = factory(Ticket::class)->create([
            'user_id' => $user->id,
        ]);
        $agentTicket = factory(Ticket::class)->create()->assignToAgent($agent);
        $agent2Ticket = factory(Ticket::class)->create()->assignToAgent($agent2);
        $teamTicket = factory(Ticket::class)->create()->assignToPool($team);

        $this->be($super->user);

        $this->visit(self::URI)
            ->assertResponseOk()
            ->see($agentTicket->content->title())
            ->see($teamTicket->content->title())
            ->see($userTicket->content->title())
            ->see($agent2Ticket->content->title());
    }

    /**
     * @group acc
     * @group acc.ticket
     * @group acc.ticket.index
     * @test
     */
    public function for_less_than_25_tickets_the_see_more_button_is_disabled()
    {
        $user = $this->makeUser();

        factory(Ticket::class, 24)->create([
            'user_id' => $user->id,
        ]);

        $this->be($user);

        $this->visit(self::URI)
            ->assertResponseOk()
            ->see('<a id="open-see-more" class="button is-disabled">No more to show...</a>');
    }

    /**
     * @group acc
     * @group acc.ticket
     * @group acc.ticket.index
     * @test
     */
    public function for_more_than_25_tickets_the_see_more_button_is_enabled()
    {
        $user = $this->makeUser();

        factory(Ticket::class, 26)->create([
            'user_id' => $user->id,
        ]);

        $this->be($user);

        $this->visit(self::URI)
            ->assertResponseOk()
            ->see('<a id="open-see-more" class="button" href=');
    }
}
