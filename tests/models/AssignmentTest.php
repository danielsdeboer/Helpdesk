<?php

namespace Aviator\Helpdesk\Tests;

use Illuminate\Support\Facades\Notification;
use Aviator\Helpdesk\Notifications\Internal\AssignedToAgent;

class AssignmentTest extends TestCase
{
    /** @test */
    public function creating_an_assignment_creates_an_action_via_the_assignment_observer()
    {
        $assignment = $this->make->assignment;

        $this->assertEquals('Assigned', $assignment->action->name);
    }

    /** @test */
    public function create_an_assignment_fires_a_notification_to_the_assignee()
    {
        $assignment = $this->make->assignment;

        /* @noinspection PhpUndefinedMethodInspection */
        Notification::assertSentTo(
            $assignment->assignee->user,
            AssignedToAgent::class
        );
    }
}
