<?php

namespace Aviator\Helpdesk\Observers;

use Aviator\Helpdesk\Models\TeamAssignment;
use Aviator\Helpdesk\Observers\Abstracts\AbstractObserver;

class TeamAssignmentObserver extends AbstractObserver
{
    /**
     * Listen to the created event.
     * @param \Aviator\Helpdesk\Models\TeamAssignment $observed
     * @return void
     */
    public function created (TeamAssignment $observed)
    {
        $this->createAction('assigned to team', $observed);

        if (!$observed->ticket->is_ignored) {
            foreach ($observed->team->teamLeads as $teamLead) {
                $teamLead->user->notify(
                    $this->factory->make('assignedToTeam', $observed->ticket)
                );
            }
        }
    }
}
