<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Ticket;

class Bug28Test extends AdminBase
{
    const PUB = 'helpdesk/tickets/public/';

    /**
     * @group bugs
     * @group bugs.28
     * @test
     */
    public function ticketNoteBodiesShouldBeFormattedWithLineBreaks()
    {
        $agent = factory(Agent::class)->create();
        $ticket = factory(Ticket::class)->create();

        $ticket->note("test\nnote", $agent);

        $this->visit(self::PUB . $ticket->uuid)
            ->see("test<br>\nnote");
    }
}
