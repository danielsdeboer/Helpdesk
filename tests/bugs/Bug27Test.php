<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Ticket;

class Bug27Test extends AdminBase
{
    const PUB = 'helpdesk/tickets/public/';

    /**
     * @group bugs
     * @group bugs.27
     * @test
     */
    public function closedTicketsShouldOnlyHaveAClosedStatusTag()
    {
        $agent = factory(Agent::class)->create();
        $ticket = factory(Ticket::class)->create();

        $ticket->close(null, $agent);

        $this->visit(self::PUB . $ticket->uuid)
            ->see('<span class="tag is-info is-medium">Closed</span>')
            ->dontSee('<span class="tag is-danger is-medium">Overdue</span>')
            ->dontSee('<span class="tag is-success is-medium">On Time</span>')
            ->dontSee('<span class="tag is-danger is-medium">Not Assigned</span>')
            ->dontSee('<span class="tag is-success is-medium">Assigned</span>')
            ->dontSee('<span class="tag is-warning is-medium">Assigned To Team</span>');
    }

    /**
     * @group bugs
     * @group bugs.27
     * @test
     */
    public function openTicketsShouldNotDisplayTheClosedTag()
    {
        $ticket = factory(Ticket::class)->create();

        $this->visit(self::PUB . $ticket->uuid)
            ->dontSee('<span class="tag is-info is-medium">Closed</span>');
    }
}
