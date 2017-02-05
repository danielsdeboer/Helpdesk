<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Pool;
use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Tests\AdminBase;

class TicketsShowTest extends AdminBase
{
    const VERB = 'GET';
    const URIBASE = 'helpdesk/tickets/';
    const URI = 'helpdesk/tickets/1';

    /**
     * @group acc
     * @group acc.ticket
     * @group acc.ticket.show
     * @test
     */
    public function access_test()
    {
        $this->noGuests();
    }

    /**
     * @group acc
     * @group acc.ticket
     * @group acc.ticket.show
     * @test
     */
    public function users_can_see_their_own_tickets()
    {
        $user = factory(User::class)->create();
        $ticket = factory(Ticket::class)->create([
            'user_id' => $user->id
        ]);

        $this->be($user);

        $this->visit(self::URI)
            ->assertResponseOk()
            ->see($ticket->content->title());
    }

    /**
     * @group acc
     * @group acc.ticket
     * @group acc.ticket.show
     * @test
     */
    public function users_cant_see_other_tickets()
    {
        $user = factory(User::class)->create();
        $ticket = factory(Ticket::class)->create();

        $this->be($user);
        $this->call('GET', self::URI);

        $this->assertResponseStatus(404);
    }

    /**
     * @group acc
     * @group acc.ticket
     * @group acc.ticket.show
     * @test
     */
    public function agents_can_see_tickets_assigned_to_them()
    {
        $agent = factory(Agent::class)->create();
        $ticket = factory(Ticket::class)->create()->assignToAgent($agent);

        $this->be($agent->user);

        $this->visit(self::URI)
            ->assertResponseOk()
            ->see($ticket->content->title());
    }

    /**
     * @group acc
     * @group acc.ticket
     * @group acc.ticket.show
     * @test
     */
    public function agents_cant_see_tickets_not_assigned_to_them()
    {
        $agent = factory(Agent::class)->create();
        $ticket = factory(Ticket::class)->create();

        $this->be($agent->user);
        $this->call('GET', self::URI);

        $this->assertResponseStatus(404);
    }

    /**
     * @group acc
     * @group acc.ticket
     * @group acc.ticket.show
     * @test
     */
    public function agents_cant_see_tickets_assigned_to_another_agent()
    {
        $agent = factory(Agent::class)->create();
        $agent2 = factory(Agent::class)->create();
        $ticket = factory(Ticket::class)->create()->assignToAgent($agent2);

        $this->be($agent->user);
        $this->call('GET', self::URI);

        $this->assertResponseStatus(404);
    }

    /**
     * @group acc
     * @group acc.ticket
     * @group acc.ticket.show
     * @test
     */
    public function agents_cant_see_tickets_assigned_to_their_team_unless_it_is_assigned_to_them()
    {
        $agent = factory(Agent::class)->create();
        $team = factory(Pool::class)->create();
        $ticket = factory(Ticket::class)->create()->assignToPool($team);

        $agent->addToTeam($team);
        $this->be($agent->user);
        $this->call('GET', self::URI);

        $this->assertResponseStatus(404);
    }

    /**
     * @group acc
     * @group acc.ticket
     * @group acc.ticket.show
     * @test
     */
    public function team_leads_can_see_tickets_assigned_to_their_team()
    {
        $agent = factory(Agent::class)->create();
        $team = factory(Pool::class)->create();
        $ticket = factory(Ticket::class)->create()->assignToPool($team);

        $agent->makeTeamLeadOf($team);

        $this->be($agent->user);
        $this->call('GET', self::URI);

        $this->assertResponseOk();
    }

    /**
     * @group acc
     * @group acc.ticket
     * @group acc.ticket.show
     * @test
     */
    public function super_can_see_everthing()
    {
        $agent = factory(Agent::class)->states('isSuper')->create();
        $agent2 = factory(Agent::class)->create();
        $ticket = factory(Ticket::class)->create();

        $this->be($agent->user);
        $this->call('GET', self::URI);

        $this->assertResponseOk();

        $ticket->assignToAgent($agent2);

        $this->be($agent->user);
        $this->call('GET', self::URI);

        $this->assertResponseOk();
    }

}
