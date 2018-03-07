<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Models\Closing;
use Illuminate\Support\Facades\Notification;
use Aviator\Helpdesk\Notifications\External\Closed;

class ClosingTest extends TestCase
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

        /* @noinspection PhpUndefinedMethodInspection */
        Notification::assertSentTo(
            $closing->ticket->user,
            Closed::class
        );
    }

    /** @test */
    public function if_user_is_null_dont_send_notification()
    {
        $ticket = factory(Ticket::class)->create();
        $user = $ticket->user;
        $ticket->user->delete();

        $closing = Closing::query()->create([
            'note' => 'test note',
            'agent_id' => 1,
            'is_visible' => true,
            'ticket_id' => $ticket->id,
        ]);

        /* @noinspection PhpUndefinedMethodInspection */
        Notification::assertNotSentTo(
            //$closing->ticket->user,
            $user,
            Closed::class
        );
    }
}
