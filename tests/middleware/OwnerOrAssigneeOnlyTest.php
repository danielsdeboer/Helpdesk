<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Ticket;
use Illuminate\Support\Facades\Route;

class OwnerOrAssigneeOnlyTest extends TestCase
{
    /*
     * Set up ----------------------------------------------------------------------------------------------------------
     */

    protected $baseRoute = 'guarded';
    protected $route = 'guarded/1';

    /**
     * Setup.
     */
    public function setUp()
    {
        parent::setUp();

        Route::any('/guarded/{ticket}', ['middleware' => 'helpdesk.ticket.owner', function (Ticket $ticket) {
            return $ticket;
        }]);
    }

    /**
     * @return \Aviator\Helpdesk\Models\Agent
     */
    protected function makeAgent()
    {
        return factory(Agent::class)->create();
    }

    /**
     * @return \Aviator\Helpdesk\Tests\User
     */
    protected function makeUser()
    {
        return factory(User::class)->create();
    }

    /**
     * @return \Aviator\Helpdesk\Models\Ticket
     */
    protected function makeTicket()
    {
        return factory(Ticket::class)->create();
    }

    /**
     * @param \Aviator\Helpdesk\Tests\User $user
     * @return \Aviator\Helpdesk\Models\Ticket
     */
    protected function makeTicketForUser(User $user)
    {
        return factory(Ticket::class)->create([
            'user_id' => $user->id,
        ]);
    }

    /**
     * @param \Aviator\Helpdesk\Models\Ticket|null $ticket
     * @return string
     */
    protected function makeRoute(Ticket $ticket = null)
    {
        return $ticket
            ? $this->baseRoute . '/' . $ticket->id
            : 'guarded/1';
    }

    /*
     * Tests -----------------------------------------------------------------------------------------------------------
     */

    /**
     * @group middleware
     * @group middleware.owner
     * @test
     */
    public function it_aborts_with_403_for_guests()
    {
        $response = $this->call('GET', $this->route);

        $this->assertResponseStatus(403);
    }

    /**
     * @group middleware
     * @group middleware.owner
     * @test
     */
    public function it_aborts_if_the_ticket_isnt_found()
    {
        $this->be(factory(User::class)->create());

        $response = $this->call('GET', $this->route);

        $this->assertResponseStatus(403);
    }

    /**
     * @group middleware
     * @group middleware.owner
     * @test
     */
    public function it_aborts_if_the_user_doesnt_own_the_ticket()
    {
        $user = $this->makeUser();
        $this->be(factory(User::class)->create());

        $response = $this->call('GET', $this->route);

        $this->assertResponseStatus(403);
    }

    /**
     * @group middleware
     * @group middleware.owner
     * @test
     */
    public function it_proceeds_if_the_user_owns_the_ticket()
    {
        $user = $this->makeUser();
        $ticket = $this->makeTicketForUser($user);

        $this->be($user);

        $response = $this->call('GET', $this->makeRoute($ticket));

        $this->assertResponseOk();
    }

    /**
     * @group middleware
     * @group middleware.owner
     * @test
     */
    public function it_aborts_if_the_agent_isnt_assigned_to_the_ticket()
    {
        $agent = $this->makeAgent();
        $ticket = $this->makeTicket();

        $this->be($agent->user);

        $response = $this->call('GET', $this->makeRoute($ticket));

        $this->assertResponseStatus(403);
    }

    /**
     * @group middleware
     * @group middleware.owner
     * @test
     */
    public function it_proceeds_if_the_agent_is_assigned_to_the_ticket()
    {
        $agent = $this->makeAgent();
        $ticket = $this->makeTicket();

        $ticket->assignToAgent($agent);
        $this->be($agent->user);

        $response = $this->call('GET', $this->makeRoute($ticket));

        $this->assertResponseOk();
    }

    /**
     * @group middleware
     * @group middleware.owner
     * @test
     */
    public function it_proceeds_if_the_agent_is_a_collaborator()
    {
        $agent = $this->makeAgent();
        $agent2 = $this->makeAgent();

        $ticket = $this->makeTicket();

        $ticket->assignToAgent($agent);
        $ticket->addCollaborator($agent2, $agent);

        $this->be($agent2->user);

        $response = $this->call('GET', $this->makeRoute($ticket));

        $this->assertResponseOk();
    }
}
