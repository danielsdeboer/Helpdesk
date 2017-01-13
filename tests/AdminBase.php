<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Pool;
use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Tests\TestCase;
use Aviator\Helpdesk\Tests\User;

abstract class AdminBase extends TestCase
{
    /**
     * Make a super user
     * @return Agent
     */
    protected function makeSuper()
    {
        return factory(Agent::class)->states('isSuper')->create()->user;
    }

    /**
     * Make a team
     * @return Pool
     */
    protected function makeTeam()
    {
        return factory(Pool::class)->create();
    }

    /**
     * Make a user
     * @return User
     */
    protected function makeUser()
    {
        return factory(User::class)->create();
    }

    /**
     * Make an agent
     * @return Agent
     */
    protected function makeAgent()
    {
        return factory(Agent::class)->create();
    }

    /**
     * Make a ticket
     * @return Ticket
     */
    protected function makeTicket()
    {
        return factory(Ticket::class)->create();
    }

    /**
     * Deny guests
     * @return void
     */
    protected function noGuests()
    {
        $this->call(static::VERB, static::URI);

        $this->assertResponseStatus('302');
        $this->assertRedirectedTo('login');
    }

    /**
     * Deny users
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
     * Deny agents
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
