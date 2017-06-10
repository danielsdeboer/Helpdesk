<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Tests\User;

class Bug24Test extends AdminBase
{
    const PUB = 'helpdesk/tickets/public/';

    /**
     * @group bugs
     * @group bugs.24
     * @test
     */
    public function ticketShouldShowTheTicketName()
    {
        $ticket = factory(Ticket::class)->create();

        $this->visit(self::PUB . $ticket->uuid)
            ->see('<p class="title">' . $ticket->content->title() . '</p>');
    }
}
