<?php

namespace Aviator\Helpdesk\Tests\Feature;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Tests\TestCase;
use Aviator\Helpdesk\Tests\User;

class TicketsTest extends TestCase
{
    /**
     * @group feature.tickets
     * @test
     */
    public function a_guest_cannot_view_tickets()
    {
        $this->call('GET', 'helpdesk/tickets');

        $this->assertResponseStatus(302);
        $this->assertRedirectedTo('login');
    }

    /**
     * @group feature.tickets
     * @test
     */
    public function a_user_may_view_ticket()
    {
        $this->be(factory(User::class)->create());

        $response = $this->call('GET', 'helpdesk/tickets');

        $this->assertResponseOk();
    }

    /**
     * @group feature.tickets
     * @test
     */
    public function a_user_may_view_a_ticket_that_belongs_to_them()
    {
        $user = factory(User::class)->create();
        $ticket = factory(Ticket::class)->create([
            'user_id' => $user->id,
        ]);

        $this->be($user);
        $response = $this->call('GET', 'helpdesk/tickets/' . $ticket->id);

        $this->assertResponseOk();
    }

    /**
     * @group feature.tickets
     * @test
     */
    public function a_user_may_not_view_a_ticket_that_belongs_to_someone_else()
    {
        $user = factory(User::class)->create();
        $user2 = factory(User::class)->create();

        $ticket = factory(Ticket::class)->create([
            'user_id' => $user2->id,
        ]);

        $this->be($user);
        $response = $this->call('GET', 'helpdesk/tickets/' . $ticket->id);

        $this->assertResponseStatus(403);
    }

    /**
     * @group feature.tickets
     * @test
     */
    public function an_agent_may_view_tickets_assigned_to_them()
    {
        $agent = factory(Agent::class)->create();
        $ticket = factory(Ticket::class)->create()->assignToAgent($agent);

        $this->be($agent->user);
        $response = $this->call('GET', 'helpdesk/tickets/' . $ticket->id);

        $this->assertResponseOk();
    }

    /**
     * @group feature.tickets
     * @test
     */
    public function an_agent_may_not_view_tickets_assigned_to_other_agents()
    {
        $agent = factory(Agent::class)->create();
        $agent2 = factory(Agent::class)->create();

        $ticket = factory(Ticket::class)->create()->assignToAgent($agent2);

        $this->be($agent->user);
        $response = $this->call('GET', 'helpdesk/tickets/' . $ticket->id);

        $this->assertResponseStatus(403);
    }

    /**
     * @group feature.tickets
     * @test
     */
    public function the_supervisor_may_view_any_ticket()
    {
        $super = factory(User::class)->create([
            'email' => config('helpdesk.supervisor.email'),
        ]);

        $agent2 = factory(Agent::class)->create();

        $ticket = factory(Ticket::class)->create()->assignToAgent($agent2);

        $this->be($super);
        $response = $this->call('GET', 'helpdesk/tickets/' . $ticket->id);

        $this->assertResponseOk();
    }

    /**
     * @group feature.tickets
     * @test
     */
    public function tickets_are_available_publically_through_via_the_uuid()
    {
        $ticket = factory(Ticket::class)->create();

        $response = $this->call('GET', 'helpdesk/tickets/public/' . $ticket->uuid);

        $this->assertResponseOk();
    }
}