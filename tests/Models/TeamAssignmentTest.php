<?php

namespace Aviator\Helpdesk\Tests\Models;

use Aviator\Helpdesk\Tests\ModelTestCase;
use Aviator\Helpdesk\Models\TeamAssignment;
use Aviator\Helpdesk\Models\Ticket;
use Carbon\Carbon;
use Aviator\Helpdesk\Models\GenericContent;
use Aviator\Helpdesk\Models\Team;
use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Tests\User;

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
        $user = $this->make->user;
        $agent = $this->make->agent;
        $team = $this->make->team;

        $agent->makeTeamLeadOf($team);
        // $assignment = $this->make->teamAssignment($team);

        // $this->assertSentTo($assignment->team->teamLeads);

        $ticket = Ticket::query()->create([
            'user_id' => $user->id,
            'content_id' => factory(GenericContent::class)->create()->id,
            'content_type' => 'Aviator\Helpdesk\Models\GenericContent',
            'status' => 'open',
            'uuid' => 1,
            'is_ignored' => null,
        ]);

        $assignment = factory(TeamAssignment::class)->create([
            'ticket_id' => $ticket->id,
            'team_id' => $team->id,
            'agent_id' => null,
            'is_visible' => true,
        ]);

        $this->assertSentTo($assignment->team->teamLeads[0]->user);
    }

    /** @test */
    public function it_doesnt_send_a_notification_to_team_if_from_ignored_user ()
    {
        //$user = $this->make->user;
        $agent = $this->make->agent;
        $ignoredUser = $this->make->user;
        $team = $this->make->team;

        $this->addIgnoredUser([$ignoredUser->email]);

        $agent->makeTeamLeadOf($team);

        $ticket = Ticket::query()->create([
            'user_id' => $ignoredUser->id,
            'content_id' => factory(GenericContent::class)->create()->id,
            'content_type' => 'Aviator\Helpdesk\Models\GenericContent',
            'status' => 'open',
            'uuid' => 1,
        ]);

        //dd(User::find(4), $ignoredUser->id);
        //$ticket->assignToTeam($team, null, true);
        //dd($ticket);

        TeamAssignment::query()->create([
            'ticket_id' => $ticket->id,
            'agent_id' => null,
            'team_id' => $team->id,
            'is_visible' => true,
        ]);
        //dd($ignoredUser, $agent->user);
        $this->assertSentTo($ignoredUser);
        // $this->assertNotSentTo($agent);
    }
}
