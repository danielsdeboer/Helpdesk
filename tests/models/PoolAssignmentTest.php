<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Pool;
use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\PoolAssignment;
use Illuminate\Support\Facades\Notification;
use Aviator\Helpdesk\Notifications\Internal\AssignedToPool;

class PoolAssignmentTest extends TestCase
{
    /**
     * @group model
     * @group model.poolassignment
     * @test
     */
    public function creating_a_pool_assignment_creates_an_action_via_the_pool_assignment_observer()
    {
        $assignment = factory(PoolAssignment::class)->create();

        $this->assertEquals('Assigned To Pool', $assignment->action->name);
    }

    /**
     * @group model
     * @group model.poolassignment
     * @test
     */
    public function create_an_assignment_fires_a_notification_to_the_assignee()
    {
        $agent = factory(Agent::class)->create();
        $pool = factory(Pool::class)->create();

        $agent->makeTeamLeadOf($pool);
        $assignment = factory(PoolAssignment::class)->create([
            'pool_id' => $pool->id,
        ]);

        Notification::assertSentTo(
            $assignment->pool->teamLeads,
            AssignedToPool::class
        );
    }
}
