<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\GenericContent;
use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Tests\TestCase;
use Aviator\Helpdesk\Tests\User;

class TicketTest extends TestCase {

    protected $user;
    protected $ticket;
    protected $content;

    protected function createUser()
    {
        $this->user = User::create([
            'email' => 'test@test.com'
        ]);
    }

    protected function createTicket()
    {
        $this->ticket = Ticket::create([
            // 'name' => 'Test Ticket',
        ]);
    }

    protected function createContent()
    {
        $this->content = GenericContent::create([
            'title' => 'Some title',
            'body' => 'Hey there!'
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

    /**
     * @group ticket
     * @test
     */
    public function a_ticket_can_have_polymorphic_generic_content()
    {
        $this->createUser();
        $this->createTicket();
        $this->createContent();

        $this->ticket->content()->associate($this->content);

        $this->assertSame($this->content, $this->ticket->content);
        $this->assertSame('Some title', $this->ticket->content->title);
    }

    /**
     * @group ticket
     * @test
     */
    public function creating_a_ticket_also_creates_an_action_via_the_ticket_observer()
    {
        $this->createTicket();

        $this->assertEquals('Created', $this->ticket->actions->first()->name);
    }

    /**
     * @group ticket
     * @test
     */
    public function a_ticket_may_be_assigned_to_a_user()
    {
        $this->createTicket();
        $this->createUser();
        $this->actingAs($this->user);

        $this->ticket->assignTo($this->user);

        $this->assertEquals('test@test.com', $this->ticket->assignment->assignee->email);
    }
}