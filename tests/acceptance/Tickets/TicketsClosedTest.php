<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Ticket;

class TicketsClosedTest extends AdminBase
{
    const VERB = 'GET';
    const URI = 'helpdesk/tickets/closed';

    /**
     * @group acc
     * @group acc.ticket
     * @group acc.ticket.closed
     * @test
     */
    public function access_test()
    {
        $this->noGuests();
    }

    /**
     * @group acc
     * @group acc.ticket
     * @group acc.ticket.closed
     * @test
     */
    public function users_can_visit()
    {
        $user = $this->makeUser();

        $this->be($user);
        $this->visit(self::URI)
            ->assertResponseOk()
            ->see('Helpdesk');
    }

    /**
     * @group acc
     * @group acc.ticket
     * @group acc.ticket.closed
     * @test
     */
    public function agents_can_visit()
    {
        $this->be($this->makeAgent()->user);

        $this->visit(self::URI)
            ->assertResponseOk()
            ->see('Helpdesk');
    }

    /**
     * @group acc
     * @group acc.ticket
     * @group acc.ticket.closed
     * @test
     */
    public function users_see_a_listing_of_their_closed_tickets()
    {
        $user = factory(User::class)->create();
        $agent = factory(Agent::class)->create();

        $ticket = factory(Ticket::class)->create([
            'user_id' => $user->id,
        ])->close(null, $agent);

        $this->be($user);

        $this->visit(self::URI)
            ->assertResponseOk()
            ->see($ticket->content->title());
    }

    /**
     * @group acc
     * @group acc.ticket
     * @group acc.ticket.closed
     * @test
     */
    public function for_more_than_25_records_pagination_is_shown()
    {
        $user = factory(User::class)->create();
        $agent = factory(Agent::class)->create();
        $ticket = factory(Ticket::class, 26)->create([
            'user_id' => $user->id,
        ])->each(function ($item) use ($agent) {
            $item->close(null, $agent);
        });

        $this->be($user);

        $this->visit(self::URI)
            ->assertResponseOk()
            ->see('<ul class="pagination-list">');
    }
}
