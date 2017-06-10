<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Ticket;

class Bug16Test extends AdminBase
{
    const PUB = 'helpdesk/tickets/public/';

    /**
     * @group bugs
     * @group bugs.16
     * @test
     */
    public function ticketShouldDisplayTheNameOfTheUserWhoClosedIt()
    {
        $user = factory(User::class)->create();
        $ticket = factory(Ticket::class)->create();
        $ticket->close(null, $user);

        $this->visit(self::PUB . $ticket->uuid)
            ->see($ticket->content->title())
            ->see('<em name="closed-by">By</em>: ' . $user->name);
    }

    /**
     * @group bugs
     * @group bugs.16
     * @test
     */
    public function ticketShouldDisplayTheNameOfTheAgentWhoClosedIt()
    {
        $agent = factory(Agent::class)->create();
        $ticket = factory(Ticket::class)->create();
        $ticket->close(null, $agent);

        $this->visit(self::PUB . $ticket->uuid)
            ->see($ticket->content->title())
            ->see('<em name="closed-by">By</em>: ' . $agent->user->name);
    }

    /**
     * @group bugs
     * @group bugs.16
     * @test
     */
    public function ticketShouldDisplayTheNameOfTheSuperuserWhoClosedIt()
    {
        $agent = factory(Agent::class)->states('isSuper')->create();
        $ticket = factory(Ticket::class)->create();
        $ticket->close(null, $agent);

        $this->visit(self::PUB . $ticket->uuid)
            ->see($ticket->content->title())
            ->see('<em name="closed-by">By</em>: ' . $agent->user->name);
    }
}
