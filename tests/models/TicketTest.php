<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Models\Collaborator;
use Aviator\Helpdesk\Models\GenericContent;
use Aviator\Helpdesk\Exceptions\CreatorRequiredException;

class TicketTest extends TestCase
{
    /** @test */
    public function it_has_an_automatically_generated_uuid()
    {
        $ticket = $this->make->ticket;

        $this->assertEquals(32, strlen($ticket->uuid));
    }

    /** @test */
    public function it_belongs_to_a_user()
    {
        $ticket = $this->make->ticket;

        $this->assertNotNull($ticket->user->email);
    }

    /** @test */
    public function it_can_have_polymorphic_generic_content()
    {
        $ticket = $this->make->ticket;
        $content = $this->make->content;

        $ticket->withContent($content);

        $this->assertSame($content, $ticket->content);
        $this->assertNotNull($ticket->content->title);
        $this->assertNotNull($ticket->content->body);
    }

    /** @test */
    public function it_can_create_the_content()
    {
        $ticket = $this->make->ticket;

        $ticket->createContent(GenericContent::class, [
            'title' => 'test title',
            'body' => 'test body',
        ]);

        $this->assertEquals('test title', $ticket->content->title);
        $this->assertEquals('test body', $ticket->content->body);
    }

    /** @test */
    public function it_may_be_assigned_to_an_agent_automatically()
    {
        $ticket = $this->make->ticket;
        $agent = $this->make->agent;

        $ticket->assignToAgent($agent);

        $this->assertEquals($agent->user->email, $ticket->assignment->assignee->user->email);
    }

    /** @test */
    public function it_may_be_assigned_to_an_agent_by_an_agent()
    {
        $ticket = $this->make->ticket;
        $agent = $this->make->agent;
        $creator = $this->make->agent;

        $ticket->assignToAgent($agent, $creator);

        $this->assertInstanceOf(Agent::class, $ticket->assignment->assignee);
        $this->assertEquals($agent->id, $ticket->assignment->assignee->id);

        $this->assertInstanceOf(Agent::class, $ticket->assignment->agent);
        $this->assertEquals($creator->id, $ticket->assignment->agent->id);
    }

    /** @test */
    public function it_may_be_assigned_to_an_assignment_team_automatically()
    {
        $ticket = $this->make->ticket;
        $team = $this->make->team;

        $ticket->assignToTeam($team);

        $this->assertEquals($team->team_lead, $ticket->teamAssignment->team->team_lead);
    }

    /** @test */
    public function it_may_be_assigned_to_an_assignment_team_by_an_agent()
    {
        $ticket = $this->make->ticket;
        $team = $this->make->team;
        $creator = $this->make->agent;

        $ticket->assignToTeam($team, $creator);

        $this->assertEquals($team->team_lead, $ticket->teamAssignment->team->team_lead);
        $this->assertEquals($creator->id, $ticket->teamAssignment->agent->id);
    }

    /** @test */
    public function it_may_be_given_a_due_date_automatically()
    {
        $ticket = $this->make->ticket;

        $ticket->dueOn('+1 day');

        $this->assertNotNull($ticket->dueDate->due_on);
    }

    /** @test */
    public function it_may_be_given_a_due_date_by_a_user()
    {
        $ticket = $this->make->ticket;
        $creator = $this->make->agent;

        $ticket->dueOn('+1 day', $creator);

        $this->assertNotNull($ticket->dueDate->due_on);
        $this->assertEquals($creator->id, $ticket->dueDate->agent->id);
    }

    /** @test */
    public function it_may_have_many_actions()
    {
        $ticket = $this->make->ticket;
        $agent = $this->make->agent;

        $ticket->assignToAgent($agent);
        $ticket->dueOn('today');

        $this->assertEquals(3, $ticket->actions->count());
    }

    /** @test */
    public function it_may_not_be_closed_automatically()
    {
        $ticket = $this->make->ticket;

        try {
            $ticket->close(null, null);
        } catch (CreatorRequiredException $e) {
            return;
        }

        $this->fail('A ticket should not be closed automatically');
    }

    /** @test */
    public function it_may_be_closed_with_a_note()
    {
        $ticket = $this->make->ticket;
        $user = factory(config('helpdesk.userModel'))->create();

        $ticket->close('here is a note', $user);

        $this->assertEquals('closed', $ticket->status);
        $this->assertEquals('here is a note', $ticket->closing->note);
    }

    /** @test */
    public function it_may_be_opened_after_being_closed_with_a_note()
    {
        $ticket = $this->make->ticket;
        $user = $this->make->user;

        $ticket->close(null, $user);
        $ticket->open('here is an opening note', $user);

        $this->assertEquals('open', $ticket->status);
        $this->assertEquals('here is an opening note', $ticket->opening->note);
    }

    /** @test */
    public function it_may_not_be_opened_with_no_user()
    {
        $ticket = $this->make->ticket;

        $ticket->close(null, $ticket->user);

        try {
            $ticket->open(null, null);
        } catch (CreatorRequiredException $e) {
            return;
        }

        $this->fail('Creating an opening without a creator should fail');
    }

    /** @test */
    public function it_can_create_a_note()
    {
        $ticket = $this->make->ticket;

        $ticket->note('here is the body of the note', $ticket->user);

        $this->assertNotNull($ticket->notes);
    }

    /** @test */
    public function it_can_have_many_notes()
    {
        $ticket = $this->make->ticket;

        $ticket
            ->note('note1', $ticket->user)
            ->note('note2', $ticket->user)
            ->note('note3', $ticket->user);

        $this->assertEquals(3, $ticket->notes->count());
    }

    /** @test */
    public function it_creates_notes_with_default_visibility_of_true()
    {
        $ticket = $this->make->ticket;

        $ticket->note('note1', $ticket->user);

        $this->assertTrue($ticket->notes->first()->is_visible);
    }

    /** @test */
    public function it_may_create_notes_with_visibility_set_to_false()
    {
        $ticket = $this->make->ticket;

        $ticket->note('note1', $ticket->user, false);

        $this->assertFalse($ticket->notes->first()->is_visible);
    }

    /** @test */
    public function it_may_be_replied_to_by_an_agent()
    {
        $ticket = $this->make->ticket;
        $agent = $this->make->agent;

        $ticket->internalReply('here is the body of the reply', $agent);

        $this->assertEquals($agent->id, $ticket->internalReplies->first()->agent->id);
    }

    /** @test */
    public function a_reply_created_by_an_agent_is_visible_to_the_user()
    {
        $ticket = $this->make->ticket;
        $agent = $this->make->agent;

        $ticket->internalReply('here is the body of the reply', $agent);

        $this->assertTrue($ticket->internalReplies->first()->is_visible);
    }

    /** @test */
    public function it_may_be_replied_to_by_a_user()
    {
        $ticket = $this->make->ticket;
        $user = $this->make->user;

        $ticket->externalReply('here is the body of the reply', $user);

        $this->assertEquals($user->id, $ticket->externalReplies->first()->user->id);
    }

    /** @test */
    public function it_has_a_uuid_scope()
    {
        $ticket = $this->make->ticket;

        $uuid = $ticket->uuid;
        /** @noinspection PhpUndefinedMethodInspection */
        $ticketLookupByUuid = Ticket::uuid($uuid);

        $this->assertSame($ticket->uuid, $ticketLookupByUuid->uuid);
    }

    /** @test */
    public function it_has_a_find_with_actions_static_method ()
    {
        $ticket = $this->make->ticket;

        $ticketWithActions = Ticket::findWithActions($ticket->id);

        $this->assertNotNull($ticketWithActions->actions);
        $this->assertEquals(1, $ticketWithActions->actions->count());
    }

    /** @test */
    public function it_has_an_unassigned_scope()
    {
        $tickets = $this->make->tickets(10);
        $assignee = $this->make->agent;

        $tickets->first()->assignToAgent($assignee);
        $unassignedTickets = Ticket::unassigned()->get();

        $this->assertEquals(9, $unassignedTickets->count());
    }

    /** @test */
    public function the_unassigned_scope_returns_only_open_tickets()
    {
        $tickets = $this->make->tickets(2);

        $tickets->first()->close(null, $tickets->first()->user);
        $unassignedTickets = Ticket::unassigned()->get();

        $this->assertEquals(1, $unassignedTickets->count());
        $this->assertEquals('open', $unassignedTickets->first()->status);
    }

    /** @test */
    public function it_has_assigned_scope()
    {
        $tickets = $this->make->tickets(10);
        $assignee = $this->make->agent;

        $tickets->first()->assignToAgent($assignee);
        $assignedTickets = Ticket::assigned()->get();

        $this->assertEquals(1, $assignedTickets->count());
    }

    /** @test */
    public function the_assigned_scope_returns_only_open_tickets()
    {
        $agent = $this->make->agent;

        $tickets = $this->make->tickets(10)
            ->each(function (Ticket $ticket) use ($agent) {
                $ticket->assignToAgent($agent);
            });

        $tickets->first()->close(null, $tickets->first()->user);
        $assignedTickets = Ticket::assigned()->get();

        $this->assertEquals(9, $assignedTickets->count());
        $this->assertEquals('open', $assignedTickets->first()->status);
    }

    /** @test */
    public function it_has_overdue_scope()
    {
        $tickets = $this->make->tickets(10);

        $tickets->first()->dueOn('yesterday');
        $overdueTickets = Ticket::overdue()->get();

        $this->assertEquals(1, $overdueTickets->count());
    }

    /** @test */
    public function the_overdue_scope_returns_only_open_tickets()
    {
        $tickets = $this->make->tickets(10)
            ->each(function (ticket $ticket) {
                $ticket->dueOn('yesterday');
            });

        $tickets->first()->close(null, $tickets->first()->user);
        $overdueTickets = Ticket::overdue()->get();

        $this->assertEquals(9, $overdueTickets->count());
        $this->assertEquals('open', $overdueTickets->first()->status);
    }

    /** @test */
    public function it_has_ontime_scope()
    {
        $tickets = $this->make->tickets(10);

        $tickets->first()->dueOn('tomorrow');
        $onTimeTickets = Ticket::onTime()->get();

        $this->assertEquals(1, $onTimeTickets->count());
    }

    /** @test */
    public function the_ontime_scope_returns_only_open_tickets()
    {
        /** @var \Illuminate\Support\Collection $tickets */
        $tickets = $this->make->tickets(10)
            ->each(function (Ticket $ticket) {
                $ticket->dueOn('tomorrow');
            });

        $tickets->first()->close(null, $tickets->first()->user);
        $ontime = Ticket::ontime()->get();

        $this->assertEquals(9, $ontime->count());
        $this->assertEquals('open', $ontime->first()->status);
    }

    /** @test */
    public function it_has_due_today_scope()
    {
        $tickets = $this->make->tickets(10);

        $tickets->first()->dueOn('now');
        $todaysTickets = Ticket::dueToday()->get();

        $this->assertEquals(1, $todaysTickets->count());
    }

    /** @test */
    public function the_duetoday_scope_returns_only_open_tickets()
    {
        $tickets = $this->make->tickets(2)
            ->each(function (Ticket $ticket) {
                $ticket->dueOn('now');
            });

        $tickets->first()->close(null, $tickets->first()->user);
        $today = Ticket::dueToday()->get();

        $this->assertEquals(1, $today->count());
        $this->assertEquals('open', $today->first()->status);
    }

    /** @test */
    public function it_has_opened_scope()
    {
        $tickets = $this->make->tickets(10);
        $user = $this->make->user;

        $tickets->first()->close(null, $user);
        $openTickets = Ticket::opened()->get();

        $this->assertEquals(9, $openTickets->count());
    }

    /** @test */
    public function it_has_teamed_scope()
    {
        $tickets = $this->make->tickets(10);
        $team = $this->make->team;

        $tickets->first()->assignToTeam($team);
        $tickets = Ticket::teamed()->get();

        $this->assertEquals(1, $tickets->count());
    }

    /** @test */
    public function the_teamed_scope_returns_only_open_tickets()
    {
        $tickets = $this->make->tickets(2)
            ->each(function (Ticket $ticket) {
                $team = $this->make->team;
                $ticket->assignToTeam($team);
            });

        $tickets->first()->close(null, $tickets->first()->user);
        $teamed = Ticket::teamed()->get();

        $this->assertEquals(1, $teamed->count());
        $this->assertEquals('open', $teamed->first()->status);
    }

    /** @test */
    public function it_has_with_actions_scope_which_returns_actions_sorted_ascending()
    {
        $team = $this->make->team;
        $agent = $this->make->agent;
        $ticket = $this->make->ticket
            ->dueOn('today')
            ->assignToTeam($team)
            ->internalReply('this is a reply', $agent);

        /** @noinspection PhpUndefinedMethodInspection */
        $ticket = Ticket::withActions()->find($ticket->id);

        $this->assertEquals(4, $ticket->actions->count());

        $previousId = 0;

        $ticket->actions->each(function ($item) use (&$previousId) {
            $previousId++;

            $this->assertEquals($previousId, $item->id);
        });
    }

    /** @test */
    public function it_has_an_is_open_method()
    {
        $ticket = $this->make->ticket;

        $this->assertTrue($ticket->isOpen());

        $ticket->close(null, $ticket->user);

        $this->assertFalse($ticket->isOpen());
    }

    /** @test */
    public function it_has_an_is_closed_method()
    {
        $ticket = $this->make->ticket;

        $this->assertFalse($ticket->isClosed());

        $ticket->close(null, $ticket->user);

        $this->assertTrue($ticket->isClosed());
    }

    /** @test */
    public function isOverdueReturnsTrueIfOverdue()
    {
        $ticket = $this->make->ticket;

        $ticket->dueOn('-1 day');
        $this->assertTrue($ticket->isOverdue());
    }

    /** @test */
    public function isOverdueReturnsFalseIfNotOverdue()
    {
        $ticket = $this->make->ticket;

        $ticket->dueOn('+1 day');
        $this->assertFalse($ticket->isOverdue());
    }

    /** @test */
    public function isAssignedReturnsFalseIfNotAssigned()
    {
        $ticket = $this->make->ticket;

        $this->assertFalse($ticket->isAssigned());
    }

    /** @test */
    public function isAssignedReturnsTrueIfAssignedToAnAgent()
    {
        $agent = $this->make->agent;
        $ticket = $this->make->ticket;
        $ticket->assignToAgent($agent);

        $this->assertTrue($ticket->isAssigned());
    }

    /** @test */
    public function isAssignedReturnsTrueIfAssignedToATeam()
    {
        $team = $this->make->team;
        $ticket = $this->make->ticket;
        $ticket->assignToTeam($team);

        $this->assertTrue($ticket->isAssigned());
    }

    /** @test */
    public function isAssignedToAnyAgentReturnsTrueIfAssignedToAnAgent()
    {
        $agent = $this->make->agent;
        $ticket = $this->make->ticket;
        $ticket->assignToAgent($agent);

        $this->assertTrue($ticket->isAssignedToAnyAgent());
    }

    /** @test */
    public function isAssignedToAnyAgentReturnsFalseIfUnassigned()
    {
        $ticket = $this->make->ticket;

        $this->assertFalse($ticket->isAssignedToAnyAgent());
    }

    /**
     * @test
     */
    public function checking_if_a_ticket_is_assigned_to_a_particular_agent ()
    {
        $agent = $this->make->agent;
        $assigned = $ticket = $this->make->ticket;
        $notAssigned = $ticket = $this->make->ticket;

        $assigned->assignToAgent($agent);

        $this->assertTrue($assigned->isAssignedTo($agent));
        $this->assertFalse($notAssigned->isAssignedTo($agent));
    }

    /** @test */
    public function isAssignedToAnyAgentReturnsFalseIfAssignedToTeam()
    {
        $team = $this->make->team;
        $ticket = $this->make->ticket;
        $ticket->assignToTeam($team);

        $this->assertFalse($ticket->isAssignedToAnyAgent());
    }

    /** @test */
    public function checking_if_a_ticket_is_assigned_to_any_team_and_not_an_agent ()
    {
        $agent = $this->make->agent;
        $team = $this->make->team;
        $ticket1 = $this->make->ticket;
        $ticket2 = $this->make->ticket;
        $ticket3 = $this->make->ticket;

        $ticket1->assignToTeam($team);
        $ticket3->assignToAgent($agent);

        $this->assertTrue($ticket1->isAssignedToAnyTeam());
        $this->assertFalse($ticket2->isAssignedToAnyTeam());
        $this->assertFalse($ticket3->isAssignedToAnyTeam());
    }

    /** @test */
    public function the_accessible_scope_returns_tickets_accessible_to_a_user()
    {
        $user = $this->make->user;
        $agent = $this->make->agent;

        // User should be able to see this
        $userTicket = $this->make->ticket($user);

        // But not this
        $this->make->ticket->assignToAgent($agent);

        // And not this
        $this->make->ticket;

        $tickets = Ticket::accessible($user)->get();

        $this->assertEquals(1, $tickets->count());
        $this->assertEquals($userTicket->content->title(), $tickets->first()->content->title());
    }

    /** @test */
    public function the_accessible_scope_returns_tickets_accessible_to_an_agent()
    {
        $user = $this->make->user;
        $agent = $this->make->agent;

        $this->make->ticket($user);
        $agentTicket = $this->make->ticket->assignToAgent($agent);
        $this->make->ticket;

        $tickets = Ticket::accessible($agent)->get();

        $this->assertEquals(1, $tickets->count());
        $this->assertEquals($agentTicket->content->title(), $tickets->first()->content->title());
    }

    /** @test */
    public function the_accessible_scope_returns_tickets_accessible_to_an_agent_who_is_a_team_lead()
    {
        $user = $this->make->user;
        $team = $this->make->team;
        $team2 = $this->make->team;
        $agent = $this->make->agent->makeTeamLeadOf($team)->addToTeam($team2);

        $this->make->ticket($user);
        $agentTicket = $this->make->ticket->assignToAgent($agent);
        $teamTicket = $this->make->ticket->assignToTeam($team);
        $this->make->ticket->assignToTeam($team2);

        $tickets = Ticket::accessible($agent)->get();

        $this->assertEquals(2, $tickets->count());
        $this->assertEquals($agentTicket->content->title(), $tickets->first()->content->title());
        $this->assertEquals($teamTicket->content->title(), $tickets->splice(1, 1)->first()->content->title());
    }

    /** @test */
    public function the_accessible_scope_returns_tickets_accessible_to_a_supervisor()
    {
        $user = $this->make->user;
        $team = $this->make->team;
        $team2 = $this->make->team;
        $this->make->agent;
        $super = $this->make->super;

        $this->make->ticket($user);
        $this->make->ticket;
        $this->make->ticket->assignToTeam($team);
        $this->make->ticket->assignToTeam($team2);

        $tickets = Ticket::accessible($super)->get();

        $this->assertEquals(4, $tickets->count());
    }

    /** @test */
    public function it_has_collaborators ()
    {
        $assignee = $this->make->agent;
        $collaborator = $this->make->agent;
        $ticket = $this->make->ticket->assignToAgent($assignee)->addCollaborator($collaborator, $assignee);

        $this->assertEquals(1, $ticket->collaborators->count());
        $this->assertInstanceOf(Collaborator::class, $ticket->collaborators->first());
    }

    /** @test */
    public function adding_a_collaborator ()
    {
        $ticket = $this->make->ticket;
        $owner = $this->make->agent;
        $collab = $this->make->agent;

        $ticket = $ticket->addCollaborator($collab, $owner);

        $this->assertEquals($collab->id, $ticket->collaborators->first()->agent->id);
    }

    /** @test */
    public function a_ticket_can_add_a_collaborating_agent_only_once()
    {
        $ticket = $this->make->ticket;
        $collab = $this->make->agent;
        $owner = $this->make->agent;

        $ticket->addCollaborator($collab, $owner);
        $ticket->addCollaborator($collab, $owner);

        $this->assertEquals(1, $ticket->fresh()->collaborators->count());
    }

    /** @test */
    public function removing_a_collaborator ()
    {
        $ticket = $this->make->ticket;
        $agent0 = $this->make->agent;
        $agent1 = $this->make->agent;
        $owner = $this->make->agent;

        /** @var \Aviator\Helpdesk\Models\Ticket $ticket */
        $ticket = $ticket->addCollaborator($agent0, $owner);
        $ticket = $ticket->addCollaborator($agent1, $owner);

        $this->assertEquals($agent0->id, $ticket->collaborators[0]->agent->id);
        $this->assertEquals($agent1->id, $ticket->collaborators[1]->agent->id);

        $ticket = $ticket->removeCollaborator($agent0);

        $this->assertEquals(1, $ticket->collaborators->count());

        $ticket = $ticket->removeCollaborator($agent1);

        $this->assertEquals(0, $ticket->collaborators->count());
    }

    /** @test */
    public function checking_if_an_agent_is_a_collaborator ()
    {
        $noCollab = $this->make->ticket;
        $agent = $this->make->agent;

        $this->assertFalse(
            $noCollab->hasCollaborator($agent)
        );

        $collab = $noCollab->addCollaborator($agent, $agent);

        $this->assertTrue(
            $collab->hasCollaborator($agent)
        );
    }

    /** @test */
    public function a_collaborator_created_via_the_ticket_is_visible_by_default ()
    {
        $agent = $this->make->agent;
        $ticket = $this->make->ticket->addCollaborator($agent, $agent);

        $this->assertTrue($ticket->collaborators->first()->is_visible);
    }

    /**
     * @test
     */
    public function checking_if_a_ticket_is_owned_by_a_user ()
    {
        $user = $this->make->user;

        $owned = $this->make->ticket($user);
        $notOwned = $ticket = $this->make->ticket;

        $this->assertTrue($owned->isOwnedBy($user));
        $this->assertFalse($notOwned->isOwnedBy($user));
    }
}
