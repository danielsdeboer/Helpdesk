<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Ticket;

class Bug22Test extends AdminBase
{
    const PUB = 'helpdesk/tickets/public/';

    /**
     * @group bugs
     * @group bugs.22
     * @test
     */
    public function ticketShouldDisplayTheNameOfTheUserWhoReplies()
    {
        $user = factory(User::class)->create();
        $superAgent = factory(Agent::class)->states('isSuper')->create();
        $ticket = factory(Ticket::class)->create();
        $ticket->externalReply('test reply', $user);

        $this->be($superAgent->user);

        $this->visit(self::PUB . $ticket->uuid)
            ->see($ticket->content->title())
            ->see('<em name="reply-by">By</em>: ' . $user->name);
    }
}
