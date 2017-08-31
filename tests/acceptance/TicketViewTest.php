<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Ticket;

class TicketViewTest extends TestCase
{
    /**
     * @group acc
     * @group acc.ticket
     * @group acc.ticket.user
     * @test
     */
    public function users_can_visit()
    {
        $user = factory(User::class)->create();
        $ticket = factory(Ticket::class)->create([
            'user_id' => $user->id,
        ]);

        $this->be($user);
        $this->visit('helpdesk/tickets/' . $ticket->id)
            ->see('<strong id="action-header-1">Opened</strong>');
    }

    /**
     * @group acc
     * @group acc.ticket
     * @group acc.ticket.user
     * @test
     */
    public function users_can_add_replies()
    {
        $user = factory(User::class)->create();

        $ticket = factory(Ticket::class)->create([
            'user_id' => $user->id,
        ]);

        $this->be($user);

        $this->visit('helpdesk/tickets/' . $ticket->id)
            ->see('<strong id="action-header-1">Opened</strong>')
            ->type('test reply body', 'reply_body')
            ->press('reply_submit')
            ->seePageIs('helpdesk/tickets/' . $ticket->id)
            ->see('<strong id="action-header-2">Reply Added</strong>');
    }

    /**
     * @group acc
     * @group acc.ticket
     * @group acc.ticket.user
     * @test
     */
    public function users_can_close()
    {
        $user = factory(User::class)->create();
        $ticket = factory(Ticket::class)->create([
            'user_id' => $user->id,
        ]);

        $this->be($user);
        $this->visit('helpdesk/tickets/' . $ticket->id)
            ->see('<strong id="action-header-1">Opened</strong>')
            ->press('close_submit')
            ->seePageIs('helpdesk/tickets/' . $ticket->id)
            ->see('<strong id="action-header-2">Closed</strong>');
    }

    /**
     * @group acc
     * @group acc.ticket
     * @group acc.ticket.agent
     * @test
     */
    public function agents_can_visit()
    {
        $user = factory(User::class)->create();
        $agent = factory(Agent::class)->create();
        $ticket = factory(Ticket::class)->create([
            'user_id' => $user->id,
        ])->assignToAgent($agent);

        $this->be($agent->user);
        $this->visit('helpdesk/tickets/' . $ticket->id)
            ->see('<strong id="action-header-1">Opened</strong>')
            ->see('<strong id="action-header-2">Assigned</strong>');
    }

    /**
     * @group acc
     * @group acc.ticket
     * @group acc.ticket.agent
     * @test
     */
    public function agents_can_add_private_notes()
    {
        $user = factory(User::class)->create();
        $agent = factory(Agent::class)->create();
        $ticket = factory(Ticket::class)->create([
            'user_id' => $user->id,
        ])->assignToAgent($agent);

        $this->be($agent->user);
        $this->visit('helpdesk/tickets/' . $ticket->id)
            ->see('<strong id="action-header-1">Opened</strong>')
            ->see('<strong id="action-header-2">Assigned</strong>')
            ->type('test note body', 'note_body')
            ->press('note_submit')
            ->seePageIs('helpdesk/tickets/' . $ticket->id)
            ->see('<strong id="action-header-3">Note Added</strong>')
            ->see('id="action-3-private"');
    }

    /**
     * @group acc
     * @group acc.ticket
     * @group acc.ticket.agent
     * @test
     */
    public function agents_can_add_public_notes()
    {
        $user = factory(User::class)->create();
        $agent = factory(Agent::class)->create();
        $ticket = factory(Ticket::class)->create([
            'user_id' => $user->id,
        ])->assignToAgent($agent);

        $this->be($agent->user);
        $this->visit('helpdesk/tickets/' . $ticket->id)
            ->see('<strong id="action-header-1">Opened</strong>')
            ->see('<strong id="action-header-2">Assigned</strong>')
            ->type('test note body', 'note_body')
            ->check('note_is_visible')
            ->press('note_submit')
            ->seePageIs('helpdesk/tickets/' . $ticket->id)
            ->see('<strong id="action-header-3">Note Added</strong>')
            ->see('id="action-3-public"');
    }

    /**
     * @group acc
     * @group acc.ticket
     * @group acc.ticket.agent
     * @test
     */
    public function agents_can_add_replies()
    {
        $user = factory(User::class)->create();
        $agent = factory(Agent::class)->create();
        $ticket = factory(Ticket::class)->create([
            'user_id' => $user->id,
        ])->assignToAgent($agent);

        $this->be($agent->user);
        $this->visit('helpdesk/tickets/' . $ticket->id)
            ->see('<strong id="action-header-1">Opened</strong>')
            ->see('<strong id="action-header-2">Assigned</strong>')
            ->type('test reply body', 'reply_body')
            ->press('reply_submit')
            ->seePageIs('helpdesk/tickets/' . $ticket->id)
            ->see('<strong id="action-header-3">Reply Added</strong>')
            ->see('id="action-3-public"');
    }

    /**
     * @group acc
     * @group acc.ticket
     * @group acc.ticket.agent
     * @test
     */
    public function agents_can_close()
    {
        $user = factory(User::class)->create();
        $agent = factory(Agent::class)->create();
        $ticket = factory(Ticket::class)->create([
            'user_id' => $user->id,
        ])->assignToAgent($agent);

        $this->be($agent->user);
        $this->visit('helpdesk/tickets/' . $ticket->id)
            ->see('<strong id="action-header-1">Opened</strong>')
            ->see('<strong id="action-header-2">Assigned</strong>')
            ->press('close_submit')
            ->seePageIs('helpdesk/tickets/' . $ticket->id)
            ->see('<strong id="action-header-3">Closed</strong>')
            ->see('id="action-3-public"');
    }

    /**
     * @group acc
     * @group acc.ticket
     * @group acc.ticket.agent
     * @test
     */
    public function agents_can_reopen()
    {
        $user = factory(User::class)->create();
        $agent = factory(Agent::class)->create();
        $ticket = factory(Ticket::class)->create([
            'user_id' => $user->id,
        ])->assignToAgent($agent)->close(null, $agent);

        $this->be($agent->user);
        $this->visit('helpdesk/tickets/' . $ticket->id)
            ->see('<strong id="action-header-1">Opened</strong>')
            ->see('<strong id="action-header-2">Assigned</strong>')
            ->see('<strong id="action-header-3">Closed</strong>')
            ->press('open_submit')
            ->seePageIs('helpdesk/tickets/' . $ticket->id)
            ->see('<strong id="action-header-4">Opened</strong>')
            ->see('id="action-4-public"');
    }
}
