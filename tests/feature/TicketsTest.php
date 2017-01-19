<?php

namespace Aviator\Helpdesk\Tests\Feature;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Tests\TestCase;
use Aviator\Helpdesk\Tests\User;

class TicketsTest extends TestCase
{
    /**
     * @group feature.tickets
     * @test
     */
    public function a_guest_cannot_view_tickets()
    {
        $this->call('GET', 'helpdesk/tickets');

        $this->assertResponseStatus(302);
        $this->assertRedirectedTo('login');
    }

    /**
     * @group feature.tickets
     * @test
     */
    public function a_user_may_view_ticket()
    {
        $this->be(factory(User::class)->create());

        $response = $this->call('GET', 'helpdesk/tickets');

        $this->assertResponseOk();
    }

    /**
     * @group feature.tickets
     * @test
     */
    public function a_user_may_view_a_ticket_that_belongs_to_them()
    {
        $user = factory(User::class)->create();
        $ticket = factory(Ticket::class)->create([
            'user_id' => $user->id,
        ]);

        $this->be($user);
        $response = $this->call('GET', 'helpdesk/tickets/' . $ticket->id);

        $this->assertResponseOk();
    }

    /**
     * @group feature.tickets
     * @test
     */
    public function tickets_are_available_publically_through_via_the_uuid()
    {
        $ticket = factory(Ticket::class)->create();

        $response = $this->call('GET', 'helpdesk/tickets/public/' . $ticket->uuid);

        $this->assertResponseOk();
    }

    /**
     * @group feature.tickets
     * @test
     */
    public function the_content_and_user_are_displayed_on_the_ticket_page()
    {
        $ticket = factory(Ticket::class)->create();

        $this->visit('helpdesk/tickets/public/' . $ticket->uuid);

        $this->see($ticket->content->title);
        $this->see($ticket->content->body);
        $this->see($ticket->user->name);
    }

    /**
     * @group feature.tickets
     * @test
     */
    public function open_public_tickets_have_an_close_action()
    {
        $ticket = factory(Ticket::class)->create();

        $this->visit('helpdesk/tickets/public/' . $ticket->uuid);

        $this->see('<i class="material-icons">lock_outline</i>');
    }

    /**
     * @group feature.tickets
     * @test
     */
    public function open_public_tickets_have_an_reply_action()
    {
        $ticket = factory(Ticket::class)->create();

        $this->visit('helpdesk/tickets/public/' . $ticket->uuid);

        $this->see('<i class="material-icons">reply</i>');
    }

    /**
     * @group feature.tickets
     * @test
     */
    public function open_public_tickets_dont_have_agent_actions()
    {
        $ticket = factory(Ticket::class)->create();

        $this->visit('helpdesk/tickets/public/' . $ticket->uuid);

        $this->dontSee('<i class="material-icons">note_add</i>');
        $this->dontSee('<i class="material-icons">person_pin_circle</i>');
    }

    /**
     * @group feature.tickets
     * @test
     */
    public function closed_public_tickets_have_an_open_action()
    {
        $agent = factory(Agent::class)->create();
        $ticket = factory(Ticket::class)->create()->close('with note', $agent);

        $this->visit('helpdesk/tickets/public/' . $ticket->uuid);

        $this->dontSee('<i class="material-icons">reply</i>');
        $this->dontSee('<i class="material-icons">lock_outline</i>');
        $this->see('<i class="material-icons">lock_open</i>');
    }

    /**
     * @group feature.tickets
     * @test
     */
    public function open_user_tickets_have_an_close_action()
    {
        $user = factory(User::class)->create();
        $ticket = factory(Ticket::class)->create([
            'user_id' => $user->id
        ]);

        $this->be($user);
        $this->visit('helpdesk/tickets/' . $ticket->id);

        $this->see('<i class="material-icons">lock_outline</i>');
    }

    /**
     * @group feature.tickets
     * @test
     */
    public function open_user_tickets_have_an_reply_action()
    {
        $user = factory(User::class)->create();
        $ticket = factory(Ticket::class)->create([
            'user_id' => $user->id
        ]);

        $this->be($user);
        $this->visit('helpdesk/tickets/' . $ticket->id);

        $this->see('<i class="material-icons">reply</i>');
    }

    /**
     * @group feature.tickets
     * @test
     */
    public function open_user_tickets_dont_have_agent_actions()
    {
        $user = factory(User::class)->create();
        $ticket = factory(Ticket::class)->create([
            'user_id' => $user->id
        ]);

        $this->be($user);
        $this->visit('helpdesk/tickets/' . $ticket->id);

        $this->dontSee('<i class="material-icons">note_add</i>');
        $this->dontSee('<i class="material-icons">person_pin_circle</i>');
    }

    /**
     * @group feature.tickets
     * @test
     */
    public function closed_user_tickets_have_an_open_action()
    {
        $user = factory(User::class)->create();
        $agent = factory(Agent::class)->create();
        $ticket = factory(Ticket::class)->create([
            'user_id' => $user->id
        ])->close('with note', $agent);

        $this->be($user);
        $this->visit('helpdesk/tickets/' . $ticket->id);


        $this->dontSee('<i class="material-icons">reply</i>');
        $this->dontSee('<i class="material-icons">lock_outline</i>');
        $this->see('<i class="material-icons">lock_open</i>');
    }

    /**
     * @group feature.tickets.agent
     * @test
     */
    public function open_agent_tickets_have_agent_actions()
    {
        $agent = factory(Agent::class)->create();

        $ticket = factory(Ticket::class)->create()->assignToAgent($agent);

        $this->be($agent->user);
        $this->visit('helpdesk/tickets/' . $ticket->id);

        $this->see('<i class="material-icons">lock_outline</i>');
        $this->see('<i class="material-icons">note_add</i>');
        $this->see('<i class="material-icons">reply</i>');

        $this->dontSee('<i class="material-icons">lock_open</i>');
        $this->dontSee('<i class="material-icons">person_pin_circle</i>');
    }

    /**
     * @group feature.tickets.agent
     * @test
     */
    public function closed_agent_tickets_have_an_open_action()
    {
        $agent = factory(Agent::class)->create();

        $ticket = factory(Ticket::class)->create()->assignToAgent($agent)->close('with note', $agent);

        $this->be($agent->user);
        $this->visit('helpdesk/tickets/' . $ticket->id);

        $this->see('<i class="material-icons">lock_open</i>');

        $this->dontSee('<i class="material-icons">lock_outline</i>');
        $this->dontSee('<i class="material-icons">note_add</i>');
        $this->dontSee('<i class="material-icons">reply</i>');
        $this->dontSee('<i class="material-icons">person_pin_circle</i>');
    }

    /**
     * @group feature.tickets.super
     * @test
     */
    public function open_supervisor_tickets_have_supervisor_actions()
    {
        $agent = factory(Agent::class)->states('isSuper')->create();
        $ticket = factory(Ticket::class)->create()->assignToAgent($agent);

        $this->be($agent->user);
        $this->visit('helpdesk/tickets/' . $ticket->id);

        $this->see('<i class="material-icons">lock_outline</i>');
        $this->see('<i class="material-icons">note_add</i>');
        $this->see('<i class="material-icons">reply</i>');
        $this->see('<i class="material-icons">person_pin_circle</i>');

        $this->dontSee('<i class="material-icons">lock_open</i>');
    }
}