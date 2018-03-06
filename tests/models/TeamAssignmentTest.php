<?php

namespace Aviator\Helpdesk\Tests;

use Illuminate\Support\Facades\Notification;
use Aviator\Helpdesk\Notifications\Internal\AssignedToTeam;
use Aviator\Helpdesk\Models\TeamAssignment;

class TeamAssignmentTest extends TestCase
{
    /** @test */
    public function creating_a_team_assignment_creates_an_action_via_the_team_assignment_observer()
    {
        $assignment = $this->make->teamAssignment;

        $this->assertEquals('Assigned To Team', $assignment->action->name);
    }

    /** @test */
    public function create_an_assignment_fires_a_notification_to_the_assignee()
    {
        $team = $this->make->team;
        $assignment = $this->make->teamAssignment($team);
        $this->make->agent->makeTeamLeadOf($team);

        /* @noinspection PhpUndefinedMethodInspection */
        Notification::assertSentTo(
            $assignment->team->teamLeads,
            AssignedToTeam::class
        );
    }
}
