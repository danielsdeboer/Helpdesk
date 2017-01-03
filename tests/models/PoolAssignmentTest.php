<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\PoolAssignment;
use Aviator\Helpdesk\Notifications\Internal\AssignedToPool;
use Illuminate\Support\Facades\Notification;

class PoolAssignmentTest extends TestCase {

    /**
     * @group assignment
     * @test
     */
    public function creating_a_pool_assignment_creates_an_action_via_the_pool_assignment_observer()
    {
        $assignment = factory(PoolAssignment::class)->create();

        $this->assertEquals('Assigned To Pool', $assignment->action->name);
    }

    /**
     * @group assignment
     * @test
     */
    public function create_an_assignment_fires_a_notification_to_the_assignee()
    {
        $assignment = factory(PoolAssignment::class)->create();

        Notification::assertSentTo(
            $assignment->pool->teamLead,
            AssignedToPool::class
        );
    }
}