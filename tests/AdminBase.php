<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Pool;
use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Ticket;

abstract class AdminBase extends TestCase
{
    /**
     * Make a super user.
     * @return Agent
     */
    protected function makeSuper()
    {
        return factory(Agent::class)->states('isSuper')->create()->user;
    }

    /**
     * Make a team.
     * @return Pool
     */
    protected function makeTeam()
    {
        return factory(Pool::class)->create();
    }

    /**
     * Make a user.
     * @return User
     */
    protected function makeUser()
    {
        return factory(User::class)->create();
    }

    /**
     * Make an agent.
     * @return Agent
     */
    protected function makeAgent()
    {
        return factory(Agent::class)->create();
    }

    /**
     * Make a ticket.
     * @return Ticket
     */
    protected function makeTicket()
    {
        return factory(Ticket::class)->create();
    }

    /**
     * Act as the supervisor.
     * @return void
     */
    protected function beSuper()
    {
        $this->be($this->makeSuper());
    }

    /**
     * Call the uri with the verb and optional request.
     * @return void
     */
    protected function callUri($request = [])
    {
        $this->call(static::VERB, static::URI, $request);
    }

    /**
     * Die and dump witht the response content.
     * @return void
     */
    protected function ddc()
    {
        dd($this->response->content());
    }

    protected function assertValidationFailed($errors)
    {
        $this->assertResponseStatus(302);
        $this->assertSessionHasErrors($errors);
    }

    /**
     * Deny guests.
     * @return void
     */
    protected function noGuests()
    {
        $response = $this->call(static::VERB, static::URI);

        $this->assertResponseStatus('302');
        $this->assertRedirectedTo('login');
    }

    /**
     * Deny users.
     * @return void
     */
    protected function noUsers()
    {
        $user = $this->makeUser();

        $this->be($user);
        $this->call(static::VERB, static::URI);

        $this->assertResponseStatus('403');
    }

    /**
     * Deny agents.
     * @return void
     */
    protected function noAgents()
    {
        $user = $this->makeAgent()->user;

        $this->be($user);
        $this->call(static::VERB, static::URI);

        $this->assertResponseStatus('403');
    }
}
