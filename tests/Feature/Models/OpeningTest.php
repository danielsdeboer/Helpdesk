<?php

namespace Aviator\Helpdesk\Tests\Feature\Models;

use Aviator\Helpdesk\Models\GenericContent;
use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Tests\ModelTestCase;
use Aviator\Helpdesk\Tests\User;
use PHPUnit\Framework\Attributes\Test;

class OpeningTest extends ModelTestCase
{
    #[Test]
    public function creating_an_opening_creates_an_action_via_its_observer()
    {
        $opening = $this->make->opening;

        $this->assertEquals('Opened', $opening->action->name);
    }

    #[Test]
    public function creating_an_opening_fires_a_notification_to_the_end_user()
    {
        $opening = $this->make->opening;

        $this->assertSentTo($opening->ticket->user);
    }

    #[Test]
    public function if_user_is_null_on_opening_dont_send_notification()
    {
        Ticket::query()->create([
            'user_id' => 14524,
            'content_id' => GenericContent::factory()->create()->id,
            'content_type' => 'Aviator\Helpdesk\Models\GenericContent',
            'status' => 'open',
        ]);

        $this->assertNotSentTo(User::all());
    }
}
