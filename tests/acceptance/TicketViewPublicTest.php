<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Ticket;

class TicketViewPublicTest extends TestCase
{
    /*
     * Setup -----------------------------------------------------------------------------------------------------------
     */

    /**
     * @return \Aviator\Helpdesk\Models\Agent
     */
    protected function createAgent()
    {
        return factory(Agent::class)->create();
    }

    /**
     * @return \Aviator\Helpdesk\Tests\User
     */
    protected function createUser()
    {
        return factory(User::class)->create();
    }

    /**
     * @param \Aviator\Helpdesk\Tests\User $user
     * @return \Aviator\Helpdesk\Models\Ticket
     */
    protected function createTicketForUser(User $user)
    {
        return factory(Ticket::class)->create([
            'user_id' => $user->id,
        ]);
    }

    /**
     * @return \Aviator\Helpdesk\Models\Ticket
     */
    protected function createTicket()
    {
        return factory(Ticket::class)->create();
    }

    protected function buildRoute(Ticket $ticket)
    {
        return 'helpdesk/tickets/public/' . $ticket->uuid;
    }

    /*
     * Tests -----------------------------------------------------------------------------------------------------------
     */

    /**
     * @group acc
     * @group acc.public
     * @group acc.public.ticket
     * @test
     */
    public function anyone_can_visit()
    {
        $ticket = $this->createTicket();

        $this->visit($this->buildRoute($ticket))
            ->see('<strong id="action-header-1">Opened</strong>');
    }

    /**
     * @group acc
     * @group acc.public
     * @group acc.public.ticket
     * @test
     */
    public function unauthenticated_users_see_no_actions()
    {
        $ticket = $this->createTicket();

        $this->visit($this->buildRoute($ticket))
            ->dontSee('id="ticket-toolbar"');
    }
}
