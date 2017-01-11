<?php

namespace Aviator\Helpdesk\Tests\Feature;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Tests\TestCase;
use Aviator\Helpdesk\Tests\User;

class TicketReplyTest extends TestCase
{
    /**
     * @group feature
     * @group feature.tickets
     * @group feature.tickets.replies
     * @test
     */
    public function a_guest_cannot_reply()
    {
        $response = $this->call('POST', 'helpdesk/tickets/reply/' . factory(Ticket::class)->create()->id);

        $this->assertRedirectedTo('login');
    }

    /**
     * @group feature
     * @group feature.tickets
     * @group feature.tickets.replies
     * @test
     */
    public function a_user_cannot_reply_to_someone_elses_ticket()
    {
        $this->be(factory(User::class)->create());
        $response = $this->call('POST', 'helpdesk/tickets/reply/' . factory(Ticket::class)->create()->id);

        $this->assertResponseStatus(403);
    }

    /**
     * @group feature
     * @group feature.tickets
     * @group feature.tickets.replies
     * @test
     */
    public function a_user_can_reply_to_their_own_ticket()
    {
        $user = factory(User::class)->create();
        $ticket = factory(Ticket::class)->create([
            'user_id' => $user->id
        ]);

        $this->be($user);
        $response = $this->call('POST', 'helpdesk/tickets/reply/' . $ticket->id, [
            'reply_body' => 'test body'
        ]);

        $this->assertRedirectedTo('helpdesk/tickets/' . $ticket->id);
        $this->assertEquals('test body', $ticket->externalReplies->first()->body);
    }

    /**
     * @group feature
     * @group feature.tickets
     * @group feature.tickets.replies
     * @test
     */
    public function an_agent_cannot_reply_to_an_unassigned_ticket()
    {
        $agent = factory(Agent::class)->create();
        $ticket = factory(Ticket::class)->create();

        $this->be($agent->user);
        $response = $this->call('POST', 'helpdesk/tickets/reply/' . $ticket->id, [
            'body' => 'test body'
        ]);

        $this->assertResponseStatus(403);
    }

    /**
     * @group feature
     * @group feature.tickets
     * @group feature.tickets.replies
     * @test
     */
    public function an_agent_can_reply_to_an_assigned_ticket()
    {
        $agent = factory(Agent::class)->create();
        $ticket = factory(Ticket::class)->create()->assignToAgent($agent);

        $this->be($agent->user);
        $response = $this->call('POST', 'helpdesk/tickets/reply/' . $ticket->id, [
            'reply_body' => 'test body'
        ]);

        $this->assertRedirectedTo('helpdesk/tickets/' . $ticket->id);
        $this->assertEquals('test body', $ticket->internalReplies->first()->body);
    }

}