<?php

namespace Aviator\Helpdesk\Helpers\Ticket;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Team;
use Carbon\Carbon;

class Status extends TicketHelper
{
    /**
     * Is the ticket assigned to an agent or team.
     */
    public function assigned(): bool
    {
        return $this->assignedToAnAgent()
            || $this->assignedToATeam();
    }

    /**
     * Check if the ticket is assigned to a particular agent.
     */
    public function assignedTo(Agent $agent): bool
    {
        return $this->assignedToAnAgent()
            && (int) $this->ticket->assignment->assigned_to === $agent->id;
    }

    public function assignedToAnAgent(): bool
    {
        return (bool) $this->ticket->assignment;
    }

    public function assignedToTeam(Team $team): bool
    {
        return $this->assignedToATeam()
            && $this->ticket->teamAssignment->team->id === $team->id;
    }

    public function assignedToATeam(): bool
    {
        return (bool) $this->ticket->teamAssignment;
    }

    public function closed(): bool
    {
        return $this->ticket->status === 'closed';
    }

    /**
     * Is the given agent a collaborator on this ticket?
     */
    public function collaborates(Agent $agent): bool
    {
        return $this->ticket->collaborators
            ->pluck('agent.id')
            ->contains($agent->id);
    }

    public function open(): bool
    {
        return $this->ticket->status === 'open';
    }

    /**
     * Is the ticket overdue.
     */
    public function overdue(): bool
    {
        return $this->ticket->dueDate
            && $this->ticket->dueDate->due_on->lte(Carbon::now());
    }

    /**
     * Check if the ticket is owned by a user.
     */
    public function ownedBy($user): bool
    {
        return (int) $user->id === (int) $this->ticket->user_id;
    }
}
