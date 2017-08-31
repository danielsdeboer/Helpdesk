<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Ticket;

class TicketViewPublicTestTest extends TestCase
{
    /*
     * Setup -----------------------------------------------------------------------------------------------------------
     */

    /**
     * @return \Aviator\Helpdesk\Models\Agent
     */
    protected function createAgent()
    {
        return factory(Agent::class)->create();
    }

    /**
     * @return \Aviator\Helpdesk\Tests\User
     */
    protected function createUser()
    {
        return factory(User::class)->create();
    }

    /**
     * @param \Aviator\Helpdesk\Tests\User $user
     * @return \Aviator\Helpdesk\Models\Ticket
     */
    protected function createTicketForUser(User $user)
    {
        return factory(Ticket::class)->create([
            'user_id' => $user->id,
        ]);
    }

    /**
     * @return \Aviator\Helpdesk\Models\Ticket
     */
    protected function createTicket()
    {
        return factory(Ticket::class)->create();
    }

    protected function buildRoute(Ticket $ticket)
    {
        return 'helpdesk/tickets/public/' . $ticket->uuid;
    }

    /*
     * Tests -----------------------------------------------------------------------------------------------------------
     */

    /**
     * @group acc
     * @group acc.public
     * @group acc.public.ticket
     * @test
     */
    public function anyone_can_visit()
    {
        $ticket = $this->createTicket();

        $this->visit($this->buildRoute($ticket))
            ->see('<strong id="action-header-1">Opened</strong>');
    }

    /**
     * @group acc
     * @group acc.public
     * @group acc.public.ticket
     * @test
     */
    public function unauthenticated_users_see_no_actions()
    {
        $ticket = $this->createTicket();

        $this->visit($this->buildRoute($ticket))
            ->dontSee('id="ticket-toolbar"');
    }

//
//    /**
//     * @group acc
//     * @group acc.ticket
//     * @group acc.ticket.user
//     * @test
//     */
//    public function users_can_add_replies()
//    {
//        $user = $this->createUser();
//
//        $ticket = $this->createTicketForUser($user);
//
//        $this->be($user);
//
//        $this->visit('helpdesk/tickets/' . $ticket->id)
//            ->see('<strong id="action-header-1">Opened</strong>')
//            ->type('test reply body', 'reply_body')
//            ->press('reply_submit')
//            ->seePageIs('helpdesk/tickets/' . $ticket->id)
//            ->see('<strong id="action-header-2">Reply Added</strong>');
//    }
//
//    /**
//     * @group acc
//     * @group acc.ticket
//     * @group acc.ticket.user
//     * @test
//     */
//    public function users_can_close()
//    {
//        $user = $this->createUser();
//        $ticket = $this->createTicketForUser($user);
//
//        $this->be($user);
//        $this->visit('helpdesk/tickets/' . $ticket->id)
//            ->see('<strong id="action-header-1">Opened</strong>')
//            ->press('close_submit')
//            ->seePageIs('helpdesk/tickets/' . $ticket->id)
//            ->see('<strong id="action-header-2">Closed</strong>');
//    }
//
//    /**
//     * @group acc
//     * @group acc.ticket
//     * @group acc.ticket.agent
//     * @test
//     */
//    public function agents_can_visit()
//    {
//        $user = $this->createUser();
//        $agent = $this->createAgent();
//        $ticket = factory(Ticket::class)->create([
//            'user_id' => $user->id,
//        ])->assignToAgent($agent);
//
//        $this->be($agent->user);
//        $this->visit('helpdesk/tickets/' . $ticket->id)
//            ->see('<strong id="action-header-1">Opened</strong>')
//            ->see('<strong id="action-header-2">Assigned</strong>');
//    }
//
//    /**
//     * @group acc
//     * @group acc.ticket
//     * @group acc.ticket.agent
//     * @test
//     */
//    public function agents_can_add_private_notes()
//    {
//        $user = $this->createUser();
//        $agent = $this->createAgent();
//        $ticket = factory(Ticket::class)->create([
//            'user_id' => $user->id,
//        ])->assignToAgent($agent);
//
//        $this->be($agent->user);
//        $this->visit('helpdesk/tickets/' . $ticket->id)
//            ->see('<strong id="action-header-1">Opened</strong>')
//            ->see('<strong id="action-header-2">Assigned</strong>')
//            ->type('test note body', 'note_body')
//            ->press('note_submit')
//            ->seePageIs('helpdesk/tickets/' . $ticket->id)
//            ->see('<strong id="action-header-3">Note Added</strong>')
//            ->see('id="action-3-private"');
//    }
//
//    /**
//     * @group acc
//     * @group acc.ticket
//     * @group acc.ticket.agent
//     * @test
//     */
//    public function agents_can_add_public_notes()
//    {
//        $user = $this->createUser();
//        $agent = $this->createAgent();
//        $ticket = factory(Ticket::class)->create([
//            'user_id' => $user->id,
//        ])->assignToAgent($agent);
//
//        $this->be($agent->user);
//        $this->visit('helpdesk/tickets/' . $ticket->id)
//            ->see('<strong id="action-header-1">Opened</strong>')
//            ->see('<strong id="action-header-2">Assigned</strong>')
//            ->type('test note body', 'note_body')
//            ->check('note_is_visible')
//            ->press('note_submit')
//            ->seePageIs('helpdesk/tickets/' . $ticket->id)
//            ->see('<strong id="action-header-3">Note Added</strong>')
//            ->see('id="action-3-public"');
//    }
//
//    /**
//     * @group acc
//     * @group acc.ticket
//     * @group acc.ticket.agent
//     * @test
//     */
//    public function agents_can_add_replies()
//    {
//        $user = $this->createUser();
//        $agent = $this->createAgent();
//        $ticket = factory(Ticket::class)->create([
//            'user_id' => $user->id,
//        ])->assignToAgent($agent);
//
//        $this->be($agent->user);
//        $this->visit('helpdesk/tickets/' . $ticket->id)
//            ->see('<strong id="action-header-1">Opened</strong>')
//            ->see('<strong id="action-header-2">Assigned</strong>')
//            ->type('test reply body', 'reply_body')
//            ->press('reply_submit')
//            ->seePageIs('helpdesk/tickets/' . $ticket->id)
//            ->see('<strong id="action-header-3">Reply Added</strong>')
//            ->see('id="action-3-public"');
//    }
//
//    /**
//     * @group acc
//     * @group acc.ticket
//     * @group acc.ticket.agent
//     * @test
//     */
//    public function agents_can_close()
//    {
//        $user = $this->createUser();
//        $agent = $this->createAgent();
//        $ticket = factory(Ticket::class)->create([
//            'user_id' => $user->id,
//        ])->assignToAgent($agent);
//
//        $this->be($agent->user);
//        $this->visit('helpdesk/tickets/' . $ticket->id)
//            ->see('<strong id="action-header-1">Opened</strong>')
//            ->see('<strong id="action-header-2">Assigned</strong>')
//            ->press('close_submit')
//            ->seePageIs('helpdesk/tickets/' . $ticket->id)
//            ->see('<strong id="action-header-3">Closed</strong>')
//            ->see('id="action-3-public"');
//    }
//
//    /**
//     * @group acc
//     * @group acc.ticket
//     * @group acc.ticket.agent
//     * @test
//     */
//    public function agents_can_reopen()
//    {
//        $user = $this->createUser();
//        $agent = $this->createAgent();
//        $ticket = factory(Ticket::class)->create([
//            'user_id' => $user->id,
//        ])->assignToAgent($agent)->close(null, $agent);
//
//        $this->be($agent->user);
//        $this->visit('helpdesk/tickets/' . $ticket->id)
//            ->see('<strong id="action-header-1">Opened</strong>')
//            ->see('<strong id="action-header-2">Assigned</strong>')
//            ->see('<strong id="action-header-3">Closed</strong>')
//            ->press('open_submit')
//            ->seePageIs('helpdesk/tickets/' . $ticket->id)
//            ->see('<strong id="action-header-4">Opened</strong>')
//            ->see('id="action-4-public"');
//    }
//
//    /**
//     * @group acc
//     * @group acc.ticket
//     * @group acc.ticket.agent
//     * @test
//     */
//    public function agents_can_add_collaborators()
//    {
//        $user = $this->createUser();
//        $assignee = $this->createAgent();
//        $collaborator = $this->createAgent();
//
//        $ticket = $this->createTicketForUser($user);
//        $ticket = $ticket->assignToAgent($assignee)->fresh();
//
//        $this->be($assignee->user);
//
//        $this->visit('helpdesk/tickets/' . $ticket->id)
//            ->see('<strong id="action-header-1">Opened</strong>')
//            ->see('<strong id="action-header-2">Assigned</strong>')
//            ->select($collaborator->id, 'collab_id')
//            ->press('collab_submit')
//            ->seePageIs('helpdesk/tickets/' . $ticket->id)
//            ->see('<strong id="action-header-3">Collaborator Added</strong>')
//            ->see('id="action-3-public"');
//    }
}
