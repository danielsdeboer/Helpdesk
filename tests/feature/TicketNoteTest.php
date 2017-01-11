<?php

namespace Aviator\Helpdesk\Tests\Feature;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Tests\TestCase;
use Aviator\Helpdesk\Tests\User;

class TicketNotesTest extends TestCase
{
    /**
     * @group feature
     * @group feature.tickets
     * @group feature.tickets.notes
     * @test
     */
    public function a_guest_cannot_add_notes()
    {
        $response = $this->call('POST', 'helpdesk/tickets/note/' . factory(Ticket::class)->create()->id);

        $this->assertRedirectedTo('login');
    }

    /**
     * @group feature
     * @group feature.tickets
     * @group feature.tickets.notes
     * @test
     */
    public function a_user_cannot_add_notes()
    {
        $this->be(factory(User::class)->create());
        $response = $this->call('POST', 'helpdesk/tickets/close/' . factory(Ticket::class)->create()->id);

        $this->assertResponseStatus(403);
    }

    /**
     * @group feature
     * @group feature.tickets
     * @group feature.tickets.notes
     * @test
    */
    public function an_agent_cant_add_notes_to_tickets_not_assigned_to_them()
    {
        $agent = factory(Agent::class)->create();
        $ticket = factory(Ticket::class)->create();

        $this->be($agent->user);
        $response = $this->call('POST', 'helpdesk/tickets/note/' . $ticket->id, [
            'body' => 'test note'
        ]);

        $this->assertResponseStatus(403);
    }

    /**
     * @group feature
     * @group feature.tickets
     * @group feature.tickets.notes
     * @test
     */
    public function an_agent_can_add_notes_to_a_ticket_assigned_to_them()
    {
        $agent = factory(Agent::class)->create();
        $ticket = factory(Ticket::class)->create()->assignToAgent($agent);

        $this->be($agent->user);
        $response = $this->call('POST', 'helpdesk/tickets/close/' . $ticket->id, [
            'note' => 'test body'
        ]);

        $ticket = $ticket->fresh();

        $this->assertRedirectedTo('helpdesk/tickets/' . $ticket->id);
        $this->assertTrue($ticket->isClosed());
        $this->assertEquals('test body', $ticket->closing->note);
    }
}