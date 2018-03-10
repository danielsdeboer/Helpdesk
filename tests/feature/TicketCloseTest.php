<?php

namespace Aviator\Helpdesk\Tests\Feature;

use Aviator\Helpdesk\Tests\User;
use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Tests\TestCase;

class TicketCloseTest extends TestCase
{
    /**
     * @group feature
     * @group feature.tickets
     * @group feature.tickets.closings
     * @test
     */
    public function a_guest_cannot_close()
    {
        $response = $this->call('POST', 'helpdesk/tickets/close/' . factory(Ticket::class)->create()->id);

        $this->assertRedirectedTo('login');
    }

    /**
     * @group feature
     * @group feature.tickets
     * @group feature.tickets.closings
     * @test
     */
    public function a_user_cannot_close_someone_elses_ticket()
    {
        $this->be(factory(User::class)->create());
        $response = $this->call('POST', 'helpdesk/tickets/close/' . factory(Ticket::class)->create()->id);

        $this->assertResponseStatus(403);
    }

    /**
     * @group feature
     * @group feature.tickets
     * @group feature.tickets.closings
     * @test
     */
    public function a_user_can_close_to_their_own_ticket()
    {
        $user = factory(User::class)->create();
        $ticket = factory(Ticket::class)->create([
            'user_id' => $user->id,
        ]);

        $this->be($user);
        $response = $this->call('POST', 'helpdesk/tickets/close/' . $ticket->id);

        $ticket = $ticket->fresh();

        $this->assertRedirectedTo('helpdesk/tickets/' . $ticket->id);
        $this->assertTrue($ticket->status()->closed());
    }

    /**
     * @group feature
     * @group feature.tickets
     * @group feature.tickets.closings
     * @test
     */
    public function an_agent_cannot_close_an_unassigned_ticket()
    {
        $agent = factory(Agent::class)->create();
        $ticket = factory(Ticket::class)->create();

        $this->be($agent->user);
        $response = $this->call('POST', 'helpdesk/tickets/close/' . $ticket->id);

        $this->assertResponseStatus(403);
    }

    /**
     * @group feature
     * @group feature.tickets
     * @group feature.tickets.closings
     * @test
     */
    public function an_agent_can_close_an_assigned_ticket()
    {
        $agent = factory(Agent::class)->create();
        $ticket = factory(Ticket::class)->create()->assignToAgent($agent);

        $this->be($agent->user);
        $response = $this->call('POST', 'helpdesk/tickets/close/' . $ticket->id, [
            'note' => 'test body',
        ]);

        $ticket = $ticket->fresh();

        $this->assertRedirectedTo('helpdesk/tickets/' . $ticket->id);
        $this->assertTrue($ticket->status()->closed());
        $this->assertEquals('test body', $ticket->closing->note);
    }
}
