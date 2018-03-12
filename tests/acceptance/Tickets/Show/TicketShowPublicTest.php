<?php

namespace Aviator\Helpdesk\Tests;

class TicketShowPublicTest extends BKTestCase
{
    const URI = 'helpdesk/tickets/public/';

    /** @test */
    public function anyone_can_visit ()
    {
        $ticket = $this->make->ticket;

        $this->visit(self::URI . $ticket->uuid)
            ->see('<strong id="action-header-1">Opened</strong>');
    }

    /** @test */
    public function unauthenticated_users_see_no_actions ()
    {
        $ticket = $this->make->ticket;

        $this->visit(self::URI . $ticket->uuid)
            ->dontSee('id="ticket-toolbar"');
    }
}
