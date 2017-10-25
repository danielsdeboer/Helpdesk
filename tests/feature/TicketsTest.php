<?php

namespace Aviator\Helpdesk\Tests\Feature;

use Aviator\Helpdesk\Tests\TestCase;

class TicketsTest extends TestCase
{
    /** @test */
    public function a_guest_cannot_view_tickets ()
    {
        $this->get($this->make->ticketUri);

        $this->assertResponseStatus(302);
        $this->assertRedirectedTo('login');
    }

    /** @test */
    public function a_user_may_view_tickets ()
    {
        $this->be($this->make->user);

        $this->get($this->make->ticketUri);

        $this->assertResponseOk();
    }

    /** @test */
    public function a_user_may_view_a_ticket_that_belongs_to_them()
    {
        $user = $this->make->user;
        $ticket = $this->make->ticket($user);

        $this->be($user);
        $this->get($this->make->ticketUri($ticket));

        $this->assertResponseOk();
    }

    /** @test */
    public function tickets_are_available_publicly_through_via_the_uuid ()
    {
        $ticket = $this->make->ticket;

        $this->get($this->make->ticketUuidUri($ticket));

        $this->assertResponseOk();
    }

    /** @test */
    public function the_content_and_user_are_displayed_on_the_ticket_page()
    {
        $ticket = $this->make->ticket;

        $this->visit($this->make->ticketUuidUri($ticket));

        $this->see($ticket->content->title());
        $this->see($ticket->content->body);
        $this->see($ticket->user->name);
    }

    /** @test */
    public function if_the_user_is_logged_in_open_public_tickets_have_a_close_action ()
    {
        $user = $this->make->user;
        $ticket = $this->make->ticket($user);

        $this->visit($this->make->ticketUuidUri($ticket));
        $this->dontSee('<i class="material-icons">lock_outline</i>');

        $this->be($user);

        $this->visit($this->make->ticketUuidUri($ticket));
        $this->see('<i class="material-icons">lock_outline</i>');
    }

    /** @test */
    public function if_the_user_is_logged_in_open_public_tickets_have_a_reply_action ()
    {
        $user = $this->make->user;
        $ticket = $this->make->ticket($user);

        $this->visit($this->make->ticketUuidUri($ticket));
        $this->dontSee('<i class="material-icons">reply</i>');

        $this->be($user);

        $this->visit($this->make->ticketUuidUri($ticket));
        $this->see('<i class="material-icons">reply</i>');
    }

    /** @test */
    public function open_public_tickets_dont_have_agent_actions ()
    {
        $ticket = $this->make->ticket;

        $this->visit($this->make->ticketUuidUri($ticket));

        $this->dontSee('<i class="material-icons">note_add</i>');
        $this->dontSee('<i class="material-icons">person_pin_circle</i>');
    }

    /** @test */
    public function if_an_agent_is_logged_in_closed_public_tickets_have_an_open_action ()
    {
        /*
         * This is a strange test. TODO: Should this work this way?
         */
        $agent = $this->make->agent;
        $ticket = $this->make->ticket->close('with note', $agent);

        $this->be($agent->user);

        $this->visit('helpdesk/tickets/public/' . $ticket->uuid);

        $this->dontSee('<i class="material-icons">reply</i>');
        $this->dontSee('<i class="material-icons">lock_outline</i>');
        $this->see('<i class="material-icons">lock_open</i>');
    }

    /** @test */
    public function open_user_tickets_have_an_close_action()
    {
        $user = $this->make->user;
        $ticket = $this->make->ticket($user);

        $this->be($user);
        $this->visit($this->make->ticketUri($ticket));

        $this->see('<i class="material-icons">lock_outline</i>');
    }

    /** @test */
    public function open_user_tickets_have_an_reply_action()
    {
        $user = $this->make->user;
        $ticket = $this->make->ticket($user);

        $this->be($user);
        $this->visit($this->make->ticketUri($ticket));

        $this->see('<i class="material-icons">reply</i>');
    }

    /** @test */
    public function open_user_tickets_dont_have_agent_actions()
    {
        $user = $this->make->user;
        $ticket = $this->make->ticket($user);

        $this->be($user);
        $this->visit($this->make->ticketUri($ticket));

        $this->dontSee('<i class="material-icons">note_add</i>');
        $this->dontSee('<i class="material-icons">person_pin_circle</i>');
    }

    /** @test */
    public function closed_user_tickets_have_an_open_action()
    {
        $user = $this->make->user;
        $agent = $this->make->agent;
        $ticket = $this->make->ticket($user)->close('with note', $agent);

        $this->be($user);
        $this->visit($this->make->ticketUri($ticket));

        $this->dontSee('<i class="material-icons">reply</i>');
        $this->dontSee('<i class="material-icons">lock_outline</i>');
        $this->see('<i class="material-icons">lock_open</i>');
    }

    /** @test */
    public function open_agent_tickets_have_agent_actions()
    {
        $agent = $this->make->agent;
        $ticket = $this->make->ticket->assignToAgent($agent);

        $this->be($agent->user);
        $this->visit($this->make->ticketUri($ticket));

        $this->see('<i class="material-icons">lock_outline</i>');
        $this->see('<i class="material-icons">note_add</i>');
        $this->see('<i class="material-icons">reply</i>');

        $this->dontSee('<i class="material-icons">lock_open</i>');
        $this->dontSee('<i class="material-icons">person_pin_circle</i>');
    }

    /** @test */
    public function closed_agent_tickets_have_an_open_action()
    {
        $agent = $this->make->agent;
        $ticket = $this->make->ticket->assignToAgent($agent)->close('with note', $agent);

        $this->be($agent->user);
        $this->visit($this->make->ticketUri($ticket));

        $this->see('<i class="material-icons">lock_open</i>');

        $this->dontSee('<i class="material-icons">lock_outline</i>');
        $this->dontSee('<i class="material-icons">note_add</i>');
        $this->dontSee('<i class="material-icons">reply</i>');
        $this->dontSee('<i class="material-icons">person_pin_circle</i>');
    }

    /** @test */
    public function open_supervisor_tickets_have_supervisor_actions()
    {
        $super = $this->make->super;
        $ticket = $this->make->ticket->assignToAgent($super);

        $this->be($super->user);
        $this->visit($this->make->ticketUri($ticket));

        $this->see('<i class="material-icons">lock_outline</i>');
        $this->see('<i class="material-icons">note_add</i>');
        $this->see('<i class="material-icons">reply</i>');
        $this->see('<i class="material-icons">person_pin_circle</i>');

        $this->dontSee('<i class="material-icons">lock_open</i>');
    }
}
