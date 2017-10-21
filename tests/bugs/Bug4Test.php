<?php

namespace Aviator\Helpdesk\Tests;

class Bug4Test extends TestCase
{

    /** @test */
    public function guests_cant_see_public_ticket_actions ()
    {
        $ticket = $this->make->ticket;

        $this->visit('helpdesk/tickets/public/' . $ticket->uuid)
            ->assertResponseOk()
            ->see($ticket->content->title())
            ->dontSee('add reply')
            ->dontSee('close ticket');
    }
}
