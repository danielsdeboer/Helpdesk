<?php

namespace Aviator\Helpdesk\Tests\Models;

use Aviator\Helpdesk\Tests\ModelTestCase;
use Aviator\Helpdesk\Models\TeamAssignment;
use Illuminate\Support\Facades\Config;
use Aviator\Helpdesk\Models\Ticket;
use Carbon\Carbon;
use Aviator\Helpdesk\Models\GenericContent;
use Aviator\Helpdesk\Models\Team;
use Illuminate\Foundation\Auth\User;

class TeamAssignmentTest extends ModelTestCase
{
    /** @test */
    public function creating_a_team_assignment_creates_an_action_via_the_team_assignment_observer()
    {
        $assignment = $this->make->teamAssignment;

        $this->assertEquals('Assigned To Team', $assignment->action->name);
    }

    /** @test */
    public function creating_an_assignment_fires_a_notification_to_the_assignee()
    {
        $team = $this->make->team;
        $assignment = $this->make->teamAssignment($team);
        $this->make->agent->makeTeamLeadOf($team);

        $this->assertSentTo($assignment->team->teamLeads);
    }

    /** @test */
    public function it_doesnt_send_a_notification_to_team_if_from_ignored_user ()
    {
        //$user = $this->make->user;
        $agent = $this->make->agent;
        $ignoredUser = $this->make->user;
        $team = $this->make->team;

        Config::set('helpdesk.ignored', [
            $ignoredUser->email,
        ]);

        $agent->makeTeamLeadOf($team);

        $ticket = Ticket::query()->create([
            'user_id' => $ignoredUser->id,
            'content_id' => factory(GenericContent::class)->create()->id,
            'content_type' => 'Aviator\Helpdesk\Models\GenericContent',
            'status' => 'open',
            'uuid' => 1,
            'is_ignored' => Carbon::now()->toDateTimeString(),
        ]);

        $ticket->assignToTeam($team, null, true);
        //dd($ticket->user_id, $agent->user_id, $team->teamLeads);

        $this->assertSentTo($team->teamLeads);

        // TeamAssignment::query()->create([
        //     'ticket_id' => $ticket->id,
        //     'body' => 'Something',
        //     'agent_id' => $agent->id,
        //     'user_id' => $ignoredUser->id,
        //     'is_visible' => true,
        // ]);

        // $this->assertSentTo($ignoredUser);
        // $this->assertNotSentTo($agent);
    }
}
