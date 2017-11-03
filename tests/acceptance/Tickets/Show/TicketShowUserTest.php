<?php

namespace Aviator\Helpdesk\Tests;

class TicketShowUserTest extends TestCase
{
    const URI = 'helpdesk/tickets/';

    /** @test */
    public function users_can_visit ()
    {
        $user = $this->make->user;
        $ticket = $this->make->ticket($user);

        $this->be($user);

        $this->visit(self::URI . $ticket->id)
            ->see('<strong id="action-header-1">Opened</strong>');
    }

    /** @test */
    public function users_can_add_replies ()
    {
        $user = $this->make->user;
        $ticket = $this->make->ticket($user);

        $this->be($user);

        $this->visit(self::URI . $ticket->id)
            ->see('<strong id="action-header-1">Opened</strong>')
            ->type('test reply body', 'reply_body')
            ->press('reply_submit')
            ->seePageIs(self::URI . $ticket->id)
            ->see('<strong id="action-header-2">Reply Added</strong>');
    }

    /** @test */
    public function users_can_close ()
    {
        $user = $this->make->user;
        $ticket = $this->make->ticket($user);

        $this->be($user);
        $this->visit(self::URI . $ticket->id)
            ->see('<strong id="action-header-1">Opened</strong>')
            ->press('close_submit')
            ->seePageIs(self::URI . $ticket->id)
            ->see('<strong id="action-header-2">Closed</strong>');
    }

    /** @test */
    public function users_dont_see_collabs ()
    {
        $user = $this->make->user;
        $ticket = $this->make->ticket($user);

        $this->be($user);
        $this->visit(self::URI . $ticket->id)
            ->dontSee('<select name="collab-id">');
    }
}
