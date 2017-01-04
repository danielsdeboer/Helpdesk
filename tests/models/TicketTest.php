<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Assignment;
use Aviator\Helpdesk\Models\GenericContent;
use Aviator\Helpdesk\Models\Pool;
use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Notifications\External\Created;
use Aviator\Helpdesk\Notifications\External\Opened;
use Aviator\Helpdesk\Tests\TestCase;
use Aviator\Helpdesk\Tests\User;
use Illuminate\Support\Facades\Notification;

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

        $this->ticket->withContent($this->content);

        $this->assertSame($this->content, $this->ticket->content);
        $this->assertNotNull($this->ticket->content->title);
        $this->assertNotNull($this->ticket->content->body);
    }

    /**
     * @group ticket
     * @test
     */
    public function a_ticket_can_create_the_content()
    {
        $this->createTicket();

        $this->ticket->createContent(GenericContent::class, [
            'title' => 'test title',
            'body' => 'test body',
        ]);

        $this->assertEquals('test title', $this->ticket->content->title);
        $this->assertEquals('test body', $this->ticket->content->body);
    }

    /**
     * @group ticket
     * @test
     */
    public function a_ticket_may_be_assigned_to_a_user_automatically()
    {
        $this->createTicket();
        $user = factory(User::class)->create();

        $this->ticket->assignToUser($user);

        $this->assertEquals($user->email, $this->ticket->assignment->assignee->email);
    }

    /**
     * @group ticket
     * @test
     */
    public function a_ticket_may_be_assigned_to_a_user_by_a_user()
    {
        $this->createTicket();
        $user = factory(User::class)->create();
        $creator = factory(User::class)->create();

        $this->actingAs($user);

        $this->ticket->assignToUser($user, $creator);

        $this->assertEquals($user->email, $this->ticket->assignment->assignee->email);
        $this->assertEquals($creator->id, $this->ticket->assignment->creator->id);
    }

    /**
     * @group ticket
     * @test
     */
    public function a_ticket_may_be_assigned_to_an_assignment_pool_automatically()
    {
        $this->createTicket();
        $pool = factory(Pool::class)->create();

        $this->ticket->assignToPool($pool);

        $this->assertEquals($pool->team_lead, $this->ticket->poolAssignment->pool->team_lead);
    }

    /**
     * @group ticket
     * @test
     */
    public function a_ticket_may_be_assigned_to_an_assignment_pool_by_a_user()
    {
        $this->createTicket();
        $pool = factory(Pool::class)->create();
        $creator = factory(User::class)->create();

        $this->ticket->assignToPool($pool, $creator);

        $this->assertEquals($pool->team_lead, $this->ticket->poolAssignment->pool->team_lead);
        $this->assertEquals($creator->id, $this->ticket->poolAssignment->creator->id);
    }

    /**
     * @group ticket
     * @test
     */
    public function a_ticket_may_be_given_a_due_date_automatically()
    {
        $this->createTicket();

        $this->ticket->dueOn('+1 day');

        $this->assertNotNull($this->ticket->dueDate->due_on);
    }

    /**
     * @group ticket
     * @test
     */
    public function a_ticket_may_be_given_a_due_date_by_a_user()
    {
        $this->createTicket();
        $creator = factory(User::class)->create();

        $this->ticket->dueOn('+1 day', $creator);

        $this->assertNotNull($this->ticket->dueDate->due_on);
        $this->assertEquals($creator->id, $this->ticket->dueDate->creator->id);
    }

    /**
     * @group ticket
     * @test
     */
    public function a_ticket_may_have_many_actions()
    {
        $this->createTicket();

        $this->ticket->assignToUser(User::first());
        $this->ticket->dueOn('today');

        $this->assertEquals(3, $this->ticket->actions->count());
    }

    /**
     * @group ticket
     * @test
     */
    public function a_ticket_may_be_closed_automatically()
    {
        $this->createTicket();

        $this->ticket->close();

        $this->assertEquals('closed', $this->ticket->status);
    }

    /**
     * @group ticket
     * @test
     */
    public function a_ticket_may_be_closed_with_a_note()
    {
        $this->createTicket();

        $this->ticket->close('here is a note');

        $this->assertEquals('closed', $this->ticket->status);
        $this->assertEquals('here is a note', $this->ticket->closing->note);
    }

    /**
     * @group ticket
     * @test
     */
    public function a_ticket_may_be_closed_by_a_user()
    {
        $this->createTicket();
        $creator = factory(User::class)->create();

        $this->ticket->close(null, $creator);

        $this->assertEquals('closed', $this->ticket->status);
        $this->assertEquals($creator->id, $this->ticket->closing->creator->id);
    }


    /**
     * @group ticket
     * @test
     */
    public function a_ticket_may_be_opened_after_being_closed_automatically()
    {
        $this->createTicket();

        $this->ticket->close();
        $this->ticket->open();

        $this->assertEquals('open', $this->ticket->status);
    }

    /**
     * @group ticket
     * @test
     */
    public function a_ticket_may_be_opened_after_being_closed_with_a_note()
    {
        $this->createTicket();

        $this->ticket->close();
        $this->ticket->open('here is an opening note');


        $this->assertEquals('open', $this->ticket->status);
        $this->assertEquals('here is an opening note', $this->ticket->opening->note);
    }

    /**
     * @group ticket
     * @test
     */
    public function a_ticket_may_be_opened_after_being_closed_by_a_user()
    {
        $this->createTicket();
        $creator = factory(User::class)->create();

        $this->ticket->close();
        $this->ticket->open(null, $creator);

        $this->assertEquals('open', $this->ticket->status);
        $this->assertEquals($creator->id, $this->ticket->opening->creator->id);
    }

    /**
     * @group ticket
     * @test
     */
    public function a_ticket_may_be_replied_to_internally_by_a_user()
    {
        $this->createTicket();
        $creator = factory(User::class)->create();

        $this->ticket->internalReply('here is the body of the reply', $creator);

        $this->assertEquals($creator->id, $this->ticket->internalReplies->first()->creator->id);
    }

    /**
     * @group ticket
     * @test
     */
    public function a_ticket_may_not_be_replied_to_internally_automatically()
    {
        $this->createTicket();

        try {
            $this->ticket->internalReply('here is the body of the reply');
        } catch (\ErrorException $e) {
            return;
        }

        $this->fail('An internal reply should not be created without a creator');
    }

    /**
     * @group ticket
     * @test
     */
    public function an_internal_reply_created_via_the_ticket_is_visible_to_the_end_user()
    {
        $this->createTicket();
        $creator = factory(User::class)->create();

        $this->ticket->internalReply('here is the body of the reply', $creator);

        $this->assertTrue($this->ticket->internalReplies->first()->is_visible);
    }

    /**
     * @group ticket
     * @test
     */
    public function a_ticket_may_be_replied_to_externally_by_the_end_user()
    {
        $this->createTicket();
        $externalUser = factory(User::class)->create();

        $this->ticket->externalReply('here is the body of the reply', $externalUser);

        $this->assertEquals($externalUser->id, $this->ticket->externalReplies->first()->creator->id);
    }

    /**
     * @group ticket
     * @test
     */
    public function a_ticket_has_a_uuid_scope()
    {
        $this->createTicket();

        $uuid = $this->ticket->uuid;
        $ticketLookupByUuid = Ticket::uuid($uuid);

        $this->assertSame($this->ticket->uuid, $ticketLookupByUuid->uuid);
    }
}