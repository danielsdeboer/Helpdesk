<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\GenericContent;
use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Tests\TestCase;
use Aviator\Helpdesk\Tests\User;

class TicketTest extends TestCase {

    protected $ticket;
    protected $content;

    protected function createTicket()
    {
        $this->ticket = factory(Ticket::class)->create();
    }

    protected function createContent()
    {
        $this->content = factory(GenericContent::class)->create();
    }

    /**
     * @group ticket
     * @test
     */
    public function a_ticket_has_an_automatically_generated_uuid()
    {
        $this->createTicket();

        $this->assertEquals(32, strlen($this->ticket->uuid));
    }

    /**
     * @group ticket
     * @test
     */
    public function a_ticket_belongs_to_a_user()
    {
        $this->createTicket();

        $this->assertNotNull($this->ticket->user->email);
    }

    /**
     * @group ticket
     * @test
     */
    public function a_ticket_can_have_polymorphic_generic_content()
    {
        $this->createTicket();
        $this->createContent();

        $this->ticket->content()->associate($this->content);

        $this->assertSame($this->content, $this->ticket->content);
        $this->assertNotNull($this->ticket->content->title);
        $this->assertNotNull($this->ticket->content->body);
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

        $user = factory(User::class)->create();

        $this->actingAs($user);

        $this->ticket->assignTo($user);

        $this->assertEquals($user->email, $this->ticket->assignment->assignee->email);
    }
}