<?php

namespace Aviator\Helpdesk\Tests\Feature;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Tests\TestCase;

class TicketAssignmentTest extends TestCase
{
    protected $user;
    protected $agent;
    protected $ticket;

    protected function make()
    {
        $this->agent = factory(Agent::class)->states('isSuper')->create();
        $this->user = $this->agent->user;
        $this->ticket = factory(Ticket::class)->create();
    }

    /**
     * @group feature
     * @group feature.tickets
     * @group feature.tickets.assignment
     * @test
     */
    public function a_guest_cannot_assign_tickets()
    {
        $this->make();

        $response = $this->call('POST', 'helpdesk/tickets/assign/' . $this->ticket->id);

        $this->assertResponseStatus(403);
    }

    /**
     * @group feature
     * @group feature.tickets
     * @group feature.tickets.assignment
     * @test
     */
    public function an_agent_cannot_assign_tickets()
    {
        $this->make();
        $agent2 = factory(Agent::class)->create();

        $this->be($agent2->user);
        $response = $this->call('POST', 'helpdesk/tickets/assign/' . $this->ticket->id);

        $this->assertResponseStatus(403);
    }

    /**
     * @group feature
     * @group feature.tickets
     * @group feature.tickets.assignment
     * @test
     */
    public function the_supervisor_may_assign_tickets()
    {
        $this->make();
        $assignTo = factory(Agent::class)->create();

        $this->be($this->user);
        $response = $this->call('POST', 'helpdesk/tickets/assign/' . $this->ticket->id, [
            'agent_id' => $assignTo->id,
        ]);

        $this->assertRedirectedToRoute('helpdesk.tickets.show', $this->ticket->id);
        $this->assertEquals($assignTo->id, $this->ticket->assignment->assignee->id);
    }
}
