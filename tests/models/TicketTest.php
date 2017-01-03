<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Tests\TestCase;
use Aviator\Helpdesk\Tests\User;

class TicketTest extends TestCase {

    protected $user;
    protected $ticket;

    protected function createUser()
    {
        $this->user = User::create([
            'email' => 'test@test.com'
        ]);
    }

    protected function createTicket()
    {
        $this->ticket = Ticket::create([
            'name' => 'Test Ticket',
        ]);
    }

    /**
     * @group ticket
     * @test
     */
    public function a_ticket_belongs_to_a_user()
    {
        $this->createUser();
        $this->createTicket();

        $this->ticket->user()->associate($this->user);

        $this->assertEquals('test@test.com', $this->ticket->user->email);
    }
}