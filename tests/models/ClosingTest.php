<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Models\Closing;
use Aviator\Helpdesk\Notifications\External\Closed;

class ClosingTest extends AbstractModelBKTest
{
    /** @test */
    public function creating_a_closing_creates_an_action_via_its_observer()
    {
        $closing = $this->make->closing;

        $this->assertEquals('Closed', $closing->action->name);
    }

    /** @test */
    public function creating_an_closing_fires_a_notification_to_the_end_user()
    {
        $closing = $this->make->closing;

        $this->assertSentTo($closing->ticket->user);
    }

    /** @test */
    public function if_user_is_null_dont_send_notification()
    {
        $this->withoutEvents();

        $ticket = factory(Ticket::class)->create();
        $ticket->user->delete();

        $this->withEvents();

        $closing = Closing::create([
            'note' => 'test note',
            'agent_id' => 1,
            'is_visible' => true,
            'ticket_id' => $ticket->id,
        ]);

        $this->assertNotSentTo($ticket->user);
    }
}
