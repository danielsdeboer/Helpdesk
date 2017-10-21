<?php

namespace Aviator\Helpdesk\Tests;

class TicketsOpenedTest extends AdminBase
{
    const VERB = 'GET';
    const URI = 'helpdesk/tickets/open';

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
    public function users_see_a_listing_of_their_tickets ()
    {
        $user = $this->make->user;
        $ticket = $this->make->ticket($user);

        $this->be($user);

        $this->visit(self::URI)
            ->assertResponseOk()
            ->see($ticket->content->title());
    }

    /** @test */
    public function for_more_than_25_records_pagination_is_shown ()
    {
        $user = $this->make->user;
        $ticket = $this->make->tickets(26, $user);

        $this->be($user);

        $this->visit(self::URI)
            ->assertResponseOk()
            ->see('<ul class="pagination-list">');
    }
}
