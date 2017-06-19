<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Queries\OpenTicketsQuery;

class OpenTicketsQueryTest extends TestCase
{
    /**
     * @group query
     * @group query.ticket
     * @group query.ticket.open
     * @test
     */
    public function itReturnsOpenTickets()
    {
        $ticket1 = factory(Ticket::class)->create();
        $ticket2 = factory(Ticket::class)->create();
        $ticket3 = factory(Ticket::class)->create()->close(null, new Agent);

        $results = OpenTicketsQuery::builder(Ticket::query())->get();

        $this->assertEquals($results->count(), 2);
    }

    /**
     * @group query
     * @group query.ticket
     * @group query.ticket.open
     * @test
     */
    public function itReturnsTicketsBySoonestDueFirst()
    {
        $ticket1 = factory(Ticket::class)->create()->dueOn('+2 days');
        $ticket2 = factory(Ticket::class)->create()->dueOn('yesterday');
        $ticket3 = factory(Ticket::class)->create()->dueOn('+10 years');
        $ticket4 = factory(Ticket::class)->create()->dueOn('-10 years');

        $results = OpenTicketsQuery::builder(Ticket::query())->get();

        $this->assertEquals($results[0]->id, $ticket4->id);
        $this->assertEquals($results[1]->id, $ticket2->id);
        $this->assertEquals($results[2]->id, $ticket1->id);
        $this->assertEquals($results[3]->id, $ticket3->id);
    }
}
