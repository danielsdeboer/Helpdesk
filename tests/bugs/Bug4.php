<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Ticket;

class Bug4 extends AdminBase
{
    const VERB = 'GET';
    const URIBASE = 'helpdesk/tickets/';
    const URI = 'helpdesk/tickets/1';

    /**
     * @group bugs
     * @group bugs.4
     * @test
     */
    public function aGuestCantSeePublicTicketActions()
    {
        $user = $this->makeUser();
        $ticket = factory(Ticket::class)->create([
            'user_id' => $user->id,
        ]);

        $this->visit(self::URIBASE . 'public/' . $ticket->uuid)
            ->assertResponseOk()
            ->see($ticket->content->title())
            ->dontSee('add reply')
            ->dontSee('close ticket');
    }
}
