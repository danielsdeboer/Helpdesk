<?php

namespace Aviator\Helpdesk\Tests;

class TicketShowCollaboratorTest extends BKTestCase
{
    /** @const string */
    const URI = 'helpdesk/tickets';

    /** @test */
    public function collaborators_may_reply ()
    {
        $agent1 = $this->make->agent;
        $agent2 = $this->make->agent;
        $ticket = $this->make->ticket->assignToAgent($agent1)->addCollaborator($agent2, $agent1);

        $this->be($agent2->user);

        $this->visit(self::URI . '/' . $ticket->id)
            ->see('id="ticket-toolbar"')
            ->see('<p class="heading">Add Reply</p>')
            ->see('<p class="heading">Add Note</p>')
            ->dontSee('<p class="heading">Assign</p>')
            ->dontSee('<p class="heading">Close Ticket</p>')
            ->dontSee('<p class="heading">Reopen Ticket</p>')
            ->dontSee('<p class="heading">Add Collaborator</p>');
    }
}
