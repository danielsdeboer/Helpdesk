<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Queries\TicketsQuery;

class TicketsQueryTest extends TestCase
{
    /** @test */
    public function it_returns_tickets ()
    {
        $this->make->tickets(10);

        $tickets = TicketsQuery::make()->query()->get();

        $this->assertCount(10, $tickets);
    }

    /**
     * @group query
     * @group query.ticket
     * @test
     */
    public function it_returns_tickets_by_soonest_due_first ()
    {
        $ticket1 = factory(Ticket::class)->create()->dueOn('+2 days');
        $ticket2 = factory(Ticket::class)->create()->dueOn('yesterday');
        $ticket3 = factory(Ticket::class)->create()->dueOn('+10 years');
        $ticket4 = factory(Ticket::class)->create()->dueOn('-10 years');

        $results = TicketsQuery::make()
            ->orderByDueSoonest()
            ->query()
            ->get();

        $this->assertEquals($ticket4->id, $results[0]->id);
        $this->assertEquals($ticket2->id, $results[1]->id);
        $this->assertEquals($ticket1->id, $results[2]->id);
        $this->assertEquals($ticket3->id, $results[3]->id);
    }

    /**
     * @group query
     * @group query.ticket
     * @test
     */
    public function it_returns_open_only ()
    {
        $agent = factory(Agent::class)->create();
        $ticket1 = factory(Ticket::class)->create()->dueOn('+2 days');
        $ticket2 = factory(Ticket::class)->create()->dueOn('yesterday');
        $ticket3 = factory(Ticket::class)->create()->dueOn('+10 years')->close(null, $agent);
        $ticket4 = factory(Ticket::class)->create()->dueOn('-10 years');

        $results = TicketsQuery::make()
            ->orderByDueSoonest()
            ->openOnly()
            ->query()
            ->get();

        $this->assertEquals(3, $results->count());
    }

    /**
     * @group query
     * @group query.ticket
     * @test
     */
    public function itReturnsClosedOnly()
    {
        $agent = factory(Agent::class)->create();
        $ticket1 = factory(Ticket::class)->create()->dueOn('+2 days')->close(null, $agent);
        $ticket2 = factory(Ticket::class)->create()->dueOn('yesterday');
        $ticket3 = factory(Ticket::class)->create()->dueOn('+10 years');
        $ticket4 = factory(Ticket::class)->create()->dueOn('-10 years');

        $results = TicketsQuery::make()
            ->orderByDueSoonest()
            ->closedOnly()
            ->query()
            ->get();

        $this->assertEquals(1, $results->count());
    }

    /**
     * @group query
     * @group query.ticket
     * @test
     */
    public function itReturnsAUsersTickets()
    {
        $user = factory(User::class)->create();

        $ticket1 = factory(Ticket::class)->create([
            'user_id' => $user->id,
        ]);

        $ticket2 = factory(Ticket::class)->create()->dueOn('yesterday');
        $ticket3 = factory(Ticket::class)->create()->dueOn('+10 years');
        $ticket4 = factory(Ticket::class)->create()->dueOn('-10 years');

        $this->be($user);

        $results = TicketsQuery::make()
            ->query()
            ->get();

        $this->assertEquals(1, $results->count());
    }

    /**
     * @group query
     * @group query.ticket
     * @test
     */
    public function itTakesAnAgentAndReturnsOnlyTheirTickets()
    {
        $agent = factory(Agent::class)->create();
        $agent2 = factory(Agent::class)->create();

        $ticket1 = factory(Ticket::class)->create()->dueOn('+2 days')
            ->assignToAgent($agent);
        $ticket2 = factory(Ticket::class)->create()->dueOn('yesterday')
            ->assignToAgent($agent2);
        $ticket3 = factory(Ticket::class)->create()->dueOn('+10 years');
        $ticket4 = factory(Ticket::class)->create()->dueOn('-10 years');

        $results = TicketsQuery::make($agent)
            ->query()
            ->get();

        $this->assertEquals(1, $results->count());
    }
}
