<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Models\GenericContent;
use Aviator\Helpdesk\Notifications\External\Opened;

class OpeningTest extends AbstractModelBKTest
{
    /** @test */
    public function creating_an_opening_creates_an_action_via_its_observer()
    {
        $opening = $this->make->opening;

        $this->assertEquals('Opened', $opening->action->name);
    }

    /** @test */
    public function creating_an_opening_fires_a_notification_to_the_end_user()
    {
        $opening = $this->make->opening;

        $this->assertSentTo($opening->ticket->user);
    }

    /** @test */
    public function if_user_is_null_on_opening_dont_send_notification()
    {
        $ticket = Ticket::create([
            'user_id' => 14524,
            'content_id' => factory(GenericContent::class)->create()->id,
            'content_type' => 'Aviator\Helpdesk\Models\GenericContent',
            'status' => 'open',
        ]);

        $this->assertNotSentTo(User::all());
    }
}
