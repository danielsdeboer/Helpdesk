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

    protected $route = 'guarded/1';

    /**
     * Setup
     */
    public function setUp ()
    {
        parent::setUp();

        Route::any('/guarded/{ticket}', ['middleware' => 'helpdesk.ticket.owner', function (Ticket $ticket) {
            return $ticket;
        }]);
    }

    /**
     * @return \Aviator\Helpdesk\Models\Agent
     */
    protected function makeAgent ()
    {
        return factory(Agent::class)->make();
    }

    /**
     * @return \Aviator\Helpdesk\Tests\User
     */
    protected function makeUser ()
    {
        return factory(User::class)->make();
    }

    /*
     * Tests -----------------------------------------------------------------------------------------------------------
     */

    /**
     * @group middleware
     * @group middleware.owner
     * @test
     */
    public function it_aborts_with_403_for_guests ()
    {
        $response = $this->call('GET', $this->route);

        $this->assertResponseStatus(403);
    }

    /**
     * @group middleware
     * @group middleware.owner
     * @test
     */
    public function it_aborts_if_the_ticket_isnt_found ()
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
    public function it_aborts_if_the_user_doesnt_own_the_ticket ()
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
    public function it_proceeds_if_the_user_owns_the_ticket ()
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
    public function it_aborts_if_the_agent_isnt_assigned_to_the_ticket ()
    {
        $agent = $this->makeAgent();

        $this->be($agent->user);

        $response = $this->call('GET', '/guarded');

        $this->assertResponseStatus(403);
    }


}
