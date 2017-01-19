<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Pool;
use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Tests\AdminBase;

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
        $this->be($this->makeUser())
            ->visit(self::URI)
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
        $this->be($this->makeAgent()->user)
            ->visit(self::URI)
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
            'user_id' => $user->id
        ]);

        $this->be($user)
            ->visit(self::URI)
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
            'user_id' => $user->id
        ]);

        $ticket2 = factory(Ticket::class)->create([
            'user_id' => $user2->id
        ]);

        $this->be($user)
            ->visit(self::URI)
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
            'user_id' => $user->id
        ]);

        $agentTicket = factory(Ticket::class)->create()->assignToAgent($agent);

        $agent2Ticket = factory(Ticket::class)->create()->assignToAgent($agent2);

        $this->be($agent->user)
            ->visit(self::URI)
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
            'user_id' => $user->id
        ]);
        $agentTicket = factory(Ticket::class)->create()->assignToAgent($agent);
        $agent2Ticket = factory(Ticket::class)->create()->assignToAgent($agent2);
        $teamTicket = factory(Ticket::class)->create()->assignToPool($team);

        $this->be($agent->user)
            ->visit(self::URI)
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
            'user_id' => $user->id
        ]);
        $agentTicket = factory(Ticket::class)->create()->assignToAgent($agent);
        $agent2Ticket = factory(Ticket::class)->create()->assignToAgent($agent2);
        $teamTicket = factory(Ticket::class)->create()->assignToPool($team);

        $this->be($super->user)
            ->visit(self::URI)
            ->assertResponseOk()
            ->see($agentTicket->content->title())
            ->see($teamTicket->content->title())
            ->see($userTicket->content->title())
            ->see($agent2Ticket->content->title());
    }
}
