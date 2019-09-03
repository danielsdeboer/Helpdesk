<?php

namespace Aviator\Helpdesk\Helpers\Ticket;

use Carbon\Carbon;
use Aviator\Helpdesk\Models\Team;
use Aviator\Helpdesk\Models\Agent;

class Status extends TicketHelper
{
    /**
     * Is the ticket assigned to an agent or team.
     * @return bool
     */
    public function assigned (): bool
    {
        return $this->assignedToAnAgent()
            || $this->assignedToATeam();
    }

    /**
     * Check if the ticket is assigned to a particular agent.
     * @param Agent $agent
     * @return bool
     */
    public function assignedTo (Agent $agent): bool
    {
        return $this->assignedToAnAgent()
            && (int) $this->ticket->assignment->assigned_to === $agent->id;
    }

    /**
     * @return bool
     */
    public function assignedToAnAgent (): bool
    {
        return (bool) $this->ticket->assignment;
    }

    /**
     * @param Team $team
     * @return bool
     */
    public function assignedToTeam (Team $team): bool
    {
        return $this->assignedToATeam()
            && $this->ticket->teamAssignment->team->id === $team->id;
    }

    /**
     * @return bool
     */
    public function assignedToATeam (): bool
    {
        return (bool) $this->ticket->teamAssignment;
    }

    /**
     * @return bool
     */
    public function closed (): bool
    {
        return $this->ticket->status === 'closed';
    }

    /**
     * Is the given agent a collaborator on this ticket?
     * @param \Aviator\Helpdesk\Models\Agent $agent
     * @return bool
     */
    public function collaborates (Agent $agent): bool
    {
        return $this->ticket->collaborators
            ->pluck('agent.id')
            ->contains($agent->id);
    }

    /**
     * @return bool
     */
    public function open (): bool
    {
        return $this->ticket->status === 'open';
    }

    /**
     * Is the ticket overdue.
     * @return bool
     */
    public function overdue (): bool
    {
        return $this->ticket->dueDate
            && $this->ticket->dueDate->due_on->lte(Carbon::now());
    }

    /**
     * Check if the ticket is owned by a user.
     * @param $user
     * @return bool
     */
    public function ownedBy ($user): bool
    {
        return (int) $user->id === (int) $this->ticket->user_id;
    }
}
