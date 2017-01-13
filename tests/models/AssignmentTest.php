<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Assignment;
use Aviator\Helpdesk\Notifications\Internal\AssignedToAgent;
use Illuminate\Support\Facades\Notification;

class AssignmentTest extends TestCase {

    /**
     * @group model
     * @group model.assignment
     * @test
     */
    public function creating_an_assignment_creates_an_action_via_the_assignment_observer()
    {
        $assignment = factory(Assignment::class)->create();

        $this->assertEquals('Assigned', $assignment->action->name);
    }

    /**
     * @group model
     * @group model.assignment
     * @test
     */
    public function create_an_assignment_fires_a_notification_to_the_assignee()
    {
        $assignment = factory(Assignment::class)->create();

        Notification::assertSentTo(
            $assignment->assignee->user,
            AssignedToAgent::class
        );
    }
}