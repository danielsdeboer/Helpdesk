<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Ticket;

class TicketsClosedTest extends AdminBase
{
    const VERB = 'GET';
    const URI = 'helpdesk/tickets/closed';

    /** @test */
    public function access_test ()
    {
        $this->noGuests();
    }

    /** @test */
    public function users_can_visit ()
    {
        $this->be($this->make->user);

        $this->visit(self::URI)
            ->assertResponseOk()
            ->see('Helpdesk');
    }

    /** @test */
    public function agents_can_visit ()
    {
        $this->be($this->make->agent->user);

        $this->visit(self::URI)
            ->assertResponseOk()
            ->see('Helpdesk');
    }

    /** @test */
    public function users_see_a_listing_of_their_closed_tickets ()
    {
        $user = $this->make->user;
        $agent = $this->make->agent;
        $ticket = $this->make->ticket($user);

        $ticket = $ticket->close(null, $agent);
        $this->be($user);

        $this->visit(self::URI)
            ->assertResponseOk()
            ->see($ticket->content->title());
    }

    /** @test */
    public function for_more_than_25_records_pagination_is_shown ()
    {
        $user = $this->make->user;
        $agent = $this->make->agent;

        $tickets = $this->make->tickets(26, $user)
            ->each(function (Ticket $item) use ($agent) {
                $item->close(null, $agent);
            });

        $this->be($user);

        $this->visit(self::URI)
            ->assertResponseOk()
            ->see('<ul class="pagination-list">');
    }
}
