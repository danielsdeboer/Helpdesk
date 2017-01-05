<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Exceptions\CreatorRequiredException;
use Aviator\Helpdesk\Models\Assignment;
use Aviator\Helpdesk\Models\GenericContent;
use Aviator\Helpdesk\Models\Pool;
use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Notifications\External\Created;
use Aviator\Helpdesk\Notifications\External\Opened;
use Aviator\Helpdesk\Tests\TestCase;
use Aviator\Helpdesk\Tests\User;
use Carbon\Carbon;
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
    public function it_has_an_automatically_generated_uuid()
    {
        $this->createTicket();

        $this->assertEquals(32, strlen($this->ticket->uuid));
    }

    /**
     * @group ticket
     * @test
     */
    public function it_belongs_to_a_user()
    {
        $this->createTicket();

        $this->assertNotNull($this->ticket->user->email);
    }

    /**
     * @group ticket
     * @test
     */
    public function it_can_have_polymorphic_generic_content()
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
    public function it_can_create_the_content()
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
    public function it_may_be_assigned_to_a_user_automatically()
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
    public function it_may_be_assigned_to_a_user_by_a_user()
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
    public function it_may_be_assigned_to_an_assignment_pool_automatically()
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
    public function it_may_be_assigned_to_an_assignment_pool_by_a_user()
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
    public function it_may_be_given_a_due_date_automatically()
    {
        $this->createTicket();

        $this->ticket->dueOn('+1 day');

        $this->assertNotNull($this->ticket->dueDate->due_on);
    }

    /**
     * @group ticket
     * @test
     */
    public function it_may_be_given_a_due_date_by_a_user()
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
    public function it_may_have_many_actions()
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
    public function it_may_not_be_closed_automatically()
    {
        $this->createTicket();

        try {
            $this->ticket->close(null, null);
        } catch (CreatorRequiredException $e) {
            return;
        }

        $this->fail('A ticket should not be closed automatically');
    }

    /**
     * @group ticket
     * @test
     */
    public function it_may_be_closed_with_a_note()
    {
        $this->createTicket();
        $user = factory(config('helpdesk.userModel'))->create();

        $this->ticket->close('here is a note', $user);

        $this->assertEquals('closed', $this->ticket->status);
        $this->assertEquals('here is a note', $this->ticket->closing->note);
    }

    /**
     * @group ticket
     * @test
     */
    public function it_may_be_opened_after_being_closed_with_a_note()
    {
        $this->createTicket();
        $user = factory(User::class)->create();

        $this->ticket->close(null, $user);
        $this->ticket->open('here is an opening note', $user);


        $this->assertEquals('open', $this->ticket->status);
        $this->assertEquals('here is an opening note', $this->ticket->opening->note);
    }

    /**
     * @group ticket
     * @test
     */
    public function it_may_not_be_opened_with_no_user()
    {
        $this->createTicket();

        $this->ticket->close(null, $this->ticket->user);

        try {
            $this->ticket->open(null, null);

        } catch (CreatorRequiredException $e) {
            return;
        }

        $this->fail('Creating an opening without a creator should fail');
    }

    /**
     * @group ticket
     * @test
     */
    public function it_may_be_replied_to_internally_by_a_user()
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
    public function it_may_not_be_replied_to_internally_automatically()
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
    public function it_may_be_replied_to_externally_by_the_end_user()
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
    public function it_has_a_uuid_scope()
    {
        $this->createTicket();

        $uuid = $this->ticket->uuid;
        $ticketLookupByUuid = Ticket::uuid($uuid);

        $this->assertSame($this->ticket->uuid, $ticketLookupByUuid->uuid);
    }

    /**
     * @group ticket
     * @test
     */
    public function it_has_a_find_with_actions_scope()
    {
        $this->createTicket();

        $ticketWithActions = Ticket::findWithActions($this->ticket->id);

        $this->assertNotNull($ticketWithActions->actions);
        $this->assertEquals(1, $ticketWithActions->actions->count());
    }

    /**
     * @group ticket
     * @test
     */
    public function it_has_an_unassigned_scope()
    {
        $tickets = factory(Ticket::class, 10)->create();
        $assignee = factory(User::class)->create();

        $tickets->first()->assignToUser($assignee);
        $unassignedTickets = Ticket::unassigned()->get();

        $this->assertEquals(9, $unassignedTickets->count());
    }

    /**
     * @group ticket
     * @test
     */
    public function the_unassigned_scope_returns_only_open_tickets()
    {
        $tickets = factory(Ticket::class, 2)->create();

        $tickets->first()->close(null, $tickets->first()->user);
        $unassignedTickets = Ticket::unassigned()->get();

        $this->assertEquals(1, $unassignedTickets->count());
        $this->assertEquals('open', $unassignedTickets->first()->status);
    }

    /**
     * @group ticket
     * @test
     */
    public function it_has_assigned_scope()
    {
        $tickets = factory(Ticket::class, 10)->create();
        $assignee = factory(User::class)->create();

        $tickets->first()->assignToUser($assignee);
        $assignedTickets = Ticket::assigned()->get();

        $this->assertEquals(1, $assignedTickets->count());
    }

    /**
     * @group ticket
     * @test
     */
    public function the_assigned_scope_returns_only_open_tickets()
    {
        $tickets = factory(Ticket::class, 2)->create()->each(function($item) {
            $item->assignToUser($item->user);
        });

        $tickets->first()->close(null, $tickets->first()->user);
        $assignedTickets = Ticket::assigned()->get();

        $this->assertEquals(1, $assignedTickets->count());
        $this->assertEquals('open', $assignedTickets->first()->status);
    }

    /**
     * @group ticket
     * @test
     */
    public function it_has_overdue_scope()
    {
        $tickets = factory(Ticket::class, 10)->create();

        $tickets->first()->dueOn('yesterday');
        $overdueTickets = Ticket::overdue()->get();

        $this->assertEquals(1, $overdueTickets->count());
    }

    /**
     * @group ticket
     * @test
     */
    public function the_overdue_scope_returns_only_open_tickets()
    {
        $tickets = factory(Ticket::class, 2)->create()->each(function($item) {
            $item->dueOn('yesterday');
        });

        $tickets->first()->close(null, $tickets->first()->user);
        $overdueTickets = Ticket::overdue()->get();

        $this->assertEquals(1, $overdueTickets->count());
        $this->assertEquals('open', $overdueTickets->first()->status);
    }

    /**
     * @group ticket
     * @test
     */
    public function it_has_ontime_scope()
    {
        $tickets = factory(Ticket::class, 10)->create();

        $tickets->first()->dueOn('tomorrow');
        $onTimeTickets = Ticket::onTime()->get();

        $this->assertEquals(1, $onTimeTickets->count());
    }

    /**
     * @group ticket
     * @test
     */
    public function the_ontime_scope_returns_only_open_tickets()
    {
        $tickets = factory(Ticket::class, 2)->create()->each(function($item) {
            $item->dueOn('tomorrow');
        });

        $tickets->first()->close(null, $tickets->first()->user);
        $ontime = Ticket::ontime()->get();

        $this->assertEquals(1, $ontime->count());
        $this->assertEquals('open', $ontime->first()->status);
    }

    /**
     * @group ticket
     * @test
     */
    public function it_has_due_today_scope()
    {
        $tickets = factory(Ticket::class, 10)->create();

        $tickets->first()->dueOn('now');
        $todaysTickets = Ticket::dueToday()->get();

        $this->assertEquals(1, $todaysTickets->count());
    }

    /**
     * @group ticket
     * @test
     */
    public function the_duetoday_scope_returns_only_open_tickets()
    {
        $tickets = factory(Ticket::class, 2)->create()->each(function($item) {
            $item->dueOn('now');
        });

        $tickets->first()->close(null, $tickets->first()->user);
        $today = Ticket::dueToday()->get();

        $this->assertEquals(1, $today->count());
        $this->assertEquals('open', $today->first()->status);
    }


    /**
     * @group ticket
     * @test
     */
    public function it_has_opened_scope()
    {
        $tickets = factory(Ticket::class, 10)->create();
        $user = factory(config('helpdesk.userModel'))->create();

        $tickets->first()->close(null, $user);
        $openTickets = Ticket::opened()->get();

        $this->assertEquals(9, $openTickets->count());
    }

    /**
     * @group ticket
     * @test
     */
    public function it_has_pooled_scope()
    {
        $tickets = factory(Ticket::class, 10)->create();
        $pool = factory(Pool::class)->create();

        $tickets->first()->assignToPool($pool);
        $tickets = Ticket::pooled()->get();

        $this->assertEquals(1, $tickets->count());
    }

    /**
     * @group ticket
     * @test
     */
    public function the_pooled_scope_returns_only_open_tickets()
    {
        $tickets = factory(Ticket::class, 2)->create()->each(function($item) {
            $pool = factory(Pool::class)->create();

            $item->assignToPool($pool);
        });

        $tickets->first()->close(null, $tickets->first()->user);
        $pooled = Ticket::pooled()->get();

        $this->assertEquals(1, $pooled->count());
        $this->assertEquals('open', $pooled->first()->status);
    }

    /**
     * @group ticket
     * @test
     */
    public function it_has_with_actions_scope_which_returns_actions_sorted_ascending()
    {
        $ticket = factory(Ticket::class)->create();
        $pool = factory(Pool::class)->create();
        $user = factory(config('helpdesk.userModel'))->create();

        $ticket->dueOn('today')->assignToPool($pool)->internalReply('this is a reply', $user);
        $ticket = Ticket::withActions()->find($ticket->id);

        $this->assertEquals(4, $ticket->actions->count());

        $previousId = 0;

        $ticket->actions->each(function($item) use (&$previousId) {
            $previousId++;

            $this->assertEquals($previousId, $item->id);
        });
    }

    /**
     * @group ticket
     * @test
     */
    public function it_has_an_is_open_method()
    {
        $ticket = factory(Ticket::class)->create();

        $this->assertTrue($ticket->isOpen());

        $ticket->close(null, $ticket->user);

        $this->assertFalse($ticket->isOpen());
    }

    /**
     * @group ticket
     * @test
     */
    public function it_has_an_is_closed_method()
    {
        $ticket = factory(Ticket::class)->create();

        $this->assertFalse($ticket->isClosed());

        $ticket->close(null, $ticket->user);

        $this->assertTrue($ticket->isClosed());
    }
}