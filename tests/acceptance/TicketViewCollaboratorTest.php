<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Ticket;

class TicketViewCollaboratorTest extends TestCase
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

    /**
     * @param \Aviator\Helpdesk\Models\Ticket $ticket
     * @return string
     */
    protected function buildRoute(Ticket $ticket)
    {
        return 'helpdesk/tickets/' . $ticket->id;
    }

    /*
     * Tests -----------------------------------------------------------------------------------------------------------
     */

    /**
     * @group acc
     * @group acc.collab
     * @group acc.collab.ticket
     * @test
     */
    public function collaborators_may_reply()
    {
        $agent1 = $this->createAgent();
        $agent2 = $this->createAgent();
        $ticket = $this->createTicketForUser($agent1->user);
        $ticket = $ticket->addCollaborator($agent2, $agent1);

        $this->be($agent2->user);

        $this->visit($this->buildRoute($ticket))
            ->see('id="ticket-toolbar"')
            ->see('<p class="heading">Add Reply</p>')
            ->see('<p class="heading">Add Note</p>')
            ->dontSee('<p class="heading">Assign</p>')
            ->dontSee('<p class="heading">Close Ticket</p>')
            ->dontSee('<p class="heading">Reopen Ticket</p>')
            ->dontSee('<p class="heading">Add Collaborator</p>');
    }
}
