<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Agent;

class TicketShowTeamLeadTest extends TestCase
{
    const URI = 'helpdesk/tickets/';

    protected $toolbarItems = [
        'open' => [
            [
                'text' => 'Assign',
                'modal' => 'assign',
                'icon' => 'person_pin_circle',
            ], [
                'text' => 'Add Reply',
                'modal' => 'reply',
                'icon' => 'reply',
            ], [
                'text' => 'Add Note',
                'modal' => 'note',
                'icon' => 'note_add',
            ], [
                'text' => 'Close Ticket',
                'modal' => 'close',
                'icon' => 'lock_outline',
            ], [
                'text' => 'Add Collaborator',
                'modal' => 'collab',
                'icon' => 'people',
            ],
        ],

        'closed' => [
            [
                'text' => 'Reopen Ticket',
                'modal' => 'open',
                'icon' => 'lock_open',
            ],
        ],
    ];

    /**
     * @param array $item
     * @param string $method
     * @return void
     */
    protected function checkToolbarItem (array $item, string $method = 'see')
    {
        $this->$method('<div id="toolbar-action-' . $item['modal'] . '">')
            ->$method('<p class="heading">' . $item['text'] . '</p>')
            ->$method('<i class="material-icons">' . $item['icon'] . '</i>');
    }

    /**
     * Construct an option string.
     * @param Agent $agent
     * @param string $idSlug
     * @return string
     */
    protected function option (Agent $agent, string $idSlug) : string
    {
        return '<option value="' . $agent->id . '" id="' . $idSlug . $agent->id . '">' . $agent->user->name . '</option>';
    }

    /** @test */
    public function team_leads_can_visit ()
    {
        $team = $this->make->team;
        $teamLead = $this->make->teamLead($team);
        $ticket = $this->make->ticket->assignToTeam($team);

        $this->be($teamLead->user);

        $this->visit(self::URI . $ticket->id)
            ->see('<strong id="action-header-1">Opened</strong>')
            ->see('<strong id="action-assigned-to-team">Assigned To Team</strong>');
    }

    /** @test */
    public function team_leads_can_see_the_proper_open_ticket_toolbar_items ()
    {
        $team = $this->make->team;
        $teamLead = $this->make->teamLead($team);
        $ticket = $this->make->ticket->assignToTeam($team);

        $this->be($teamLead->user);
        $this->visit($this->make->ticketUri($ticket));

        /*
         * Toolbar items for open tickets.
         */
        foreach ($this->toolbarItems['open'] as $item) {
            $this->checkToolbarItem($item);
        }

        foreach ($this->toolbarItems['closed'] as $item) {
            $this->checkToolbarItem($item, 'dontSee');
        }
    }

    /** @test */
    public function team_leads_can_see_the_proper_closed_ticket_toolbar_items ()
    {
        $team = $this->make->team;
        $teamLead = $this->make->teamLead($team);
        $ticket = $this->make->ticket->assignToTeam($team)->close(null, $teamLead);

        $this->be($teamLead->user);
        $this->visit($this->make->ticketUri($ticket));

        /*
         * Items for closed.
         */
        foreach ($this->toolbarItems['closed'] as $item) {
            $this->checkToolbarItem($item);
        }

        foreach ($this->toolbarItems['open'] as $item) {
            $this->checkToolbarItem($item, 'dontSee');
        }
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
            ->see($this->option($collab, 'collab-option-'));
    }
}
