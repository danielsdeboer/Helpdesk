<?php

namespace Aviator\Helpdesk\Tests;

class TicketShowAgentTest extends TestCase
{
    const URI = 'helpdesk/tickets/';

    /** @test */
    public function agents_can_visit ()
    {
        $agent = $this->make->agent;
        $ticket = $this->make->ticket->assignToAgent($agent);

        $this->be($agent->user);
        $this->visit(self::URI . $ticket->id)
            ->see('<strong id="action-header-1">Opened</strong>')
            ->see('<strong id="action-header-2">Assigned</strong>');
    }

    /** @test */
    public function agents_can_add_private_notes ()
    {
        $agent = $this->make->agent;
        $ticket = $this->make->ticket->assignToAgent($agent);

        $this->be($agent->user);
        $this->visit(self::URI . $ticket->id)
            ->see('<strong id="action-header-1">Opened</strong>')
            ->see('<strong id="action-header-2">Assigned</strong>')
            ->type('test note body', 'note_body')
            ->press('note_submit')
            ->seePageIs(self::URI . $ticket->id)
            ->see('<strong id="action-header-3">Note Added</strong>')
            ->see('id="action-3-private"');
    }

    /** @test */
    public function agents_can_add_public_notes ()
    {
        $agent = $this->make->agent;
        $ticket = $ticket = $this->make->ticket->assignToAgent($agent);

        $this->be($agent->user);
        $this->visit(self::URI . $ticket->id)
            ->see('<strong id="action-header-1">Opened</strong>')
            ->see('<strong id="action-header-2">Assigned</strong>')
            ->type('test note body', 'note_body')
            ->check('note_is_visible')
            ->press('note_submit')
            ->seePageIs(self::URI . $ticket->id)
            ->see('<strong id="action-header-3">Note Added</strong>')
            ->see('id="action-3-public"');
    }

    /** @test */
    public function agents_can_add_replies ()
    {
        $agent = $this->make->agent;
        $ticket = $ticket = $this->make->ticket->assignToAgent($agent);

        $this->be($agent->user);
        $this->visit(self::URI . $ticket->id)
            ->see('<strong id="action-header-1">Opened</strong>')
            ->see('<strong id="action-header-2">Assigned</strong>')
            ->type('test reply body', 'reply_body')
            ->press('reply_submit')
            ->seePageIs(self::URI . $ticket->id)
            ->see('<strong id="action-header-3">Reply Added</strong>')
            ->see('id="action-3-public"');
    }

    /** @test */
    public function agents_can_close ()
    {
        $agent = $this->make->agent;
        $ticket = $ticket = $this->make->ticket->assignToAgent($agent);

        $this->be($agent->user);
        $this->visit(self::URI . $ticket->id)
            ->see('<strong id="action-header-1">Opened</strong>')
            ->see('<strong id="action-header-2">Assigned</strong>')
            ->press('close_submit')
            ->seePageIs(self::URI . $ticket->id)
            ->see('<strong id="action-header-3">Closed</strong>')
            ->see('id="action-3-public"');
    }

    /** @test */
    public function agents_can_reopen ()
    {
        $agent = $this->make->agent;
        $ticket = $ticket = $this->make->ticket->assignToAgent($agent)->close(null, $agent);

        $this->be($agent->user);
        $this->visit(self::URI . $ticket->id)
            ->see('<strong id="action-header-1">Opened</strong>')
            ->see('<strong id="action-header-2">Assigned</strong>')
            ->see('<strong id="action-header-3">Closed</strong>')
            ->press('open_submit')
            ->seePageIs(self::URI . $ticket->id)
            ->see('<strong id="action-header-4">Opened</strong>')
            ->see('id="action-4-public"');
    }

    /** @test */
    public function agents_can_add_collaborators ()
    {
        $assignee = $this->make->agent;
        $collaborator = $this->make->agent;
        $ticket = $this->make->ticket->assignToAgent($assignee);

        $this->be($assignee->user);

        $this->visit(self::URI . $ticket->id)
            ->see('<strong id="action-header-1">Opened</strong>')
            ->see('<strong id="action-header-2">Assigned</strong>')
            ->select($collaborator->id, 'collab-id')
            ->press('collab_submit')
            ->seePageIs(self::URI . $ticket->id)
            ->see('<strong id="action-header-3">Collaborator Added</strong>')
            ->see('<em>By</em>: ' . $assignee->user->name)
            ->see('id="action-3-public"');
    }
    
    /** @test */
    public function agents_can_add_collabs_outside_their_own_team ()
    {
        $assigneeTeam = $this->make->team;
        $collabTeam = $this->make->team;

        $assignee = $this->make->agent->addToTeam($assigneeTeam);
        $collab = $this->make->agent->addToTeam($collabTeam);

        $ticket = $this->make->ticket->assignToAgent($assignee);

        $this->be($assignee->user);

        $this->visit(self::URI . $ticket->id)
            ->see($this->make->option($collab, 'collab-option-'));
    }

    /** @test */
    public function team_leads_can_add_collabs_outside_their_own_team ()
    {
        $teamLeadTeam = $this->make->team;
        $collabTeam = $this->make->team;

        $teamLead = $this->make->agent->makeTeamLeadOf($teamLeadTeam);
        $collab = $this->make->agent->addToTeam($collabTeam);

        $ticket = $this->make->ticket->assignToTeam($teamLeadTeam);

        $this->be($teamLead->user);

        $this->visit(self::URI . $ticket->id)
            ->see($this->make->option($collab, 'collab-option-'));
    }

    /** @test */
    public function supers_can_add_collabs_from_any_team ()
    {
        $super = $this->make->super;
        $collab1 = $this->make->agent->addToTeam($this->make->team);
        $collab2 = $this->make->agent;

        $ticket = $this->make->ticket;

        $this->be($super->user);

        $this->visit(self::URI . $ticket->id)
            ->see($this->make->option($collab1, 'collab-option-'))
            ->see($this->make->option($collab2, 'collab-option-'));
    }

    /** @test */
    public function agents_cant_add_themselves_as_collaborator ()
    {
        $assignee = $this->make->agent;
        $ticket = $this->make->ticket->assignToAgent($assignee);

        $this->be($assignee->user);

        $this->visit(self::URI . $ticket->id)
            ->see('<strong id="action-header-1">Opened</strong>')
            ->see('<strong id="action-header-2">Assigned</strong>')
            ->select($assignee->id, 'collab-id')
            ->press('collab_submit')
            ->seePageIs(self::URI . $ticket->id)
            ->dontSee('<strong id="action-header-3">Collaborator Added</strong>')
            ->dontSee('<em>By</em>: ' . $assignee->user->name)
            ->dontSee('id="action-3-public"');
    }
}
