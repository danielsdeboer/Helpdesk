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

    /** @test */
    public function visiting_a_deleted_ticket_will_redirect_to_ticket_overview ()
    {
        $ticket = $this->make->ticket;

        $ticket->delete();

        $this->get(self::URI . $ticket->uuid)
            ->assertRedirectedTo('/helpdesk/tickets');
    }
}
