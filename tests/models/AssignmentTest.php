<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Assignment;

class AssignmentTest extends TestCase {

    /**
     * @group assignment
     * @test
     */
    public function creating_an_assignment_creates_an_action_via_the_assignment_observer()
    {
        $assignment = factory(Assignment::class)->create();

        $this->assertEquals('Assigned', $assignment->action->name);
    }
}