<?php

namespace Aviator\Helpdesk\Observers;

use Aviator\Helpdesk\Models\Action;
use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Models\TeamAssignment;
use Illuminate\Support\Facades\Notification;
use Aviator\Helpdesk\Observers\Abstracts\AbstractObserver;

class TeamAssignmentObserver extends AbstractObserver
{
    /**
     * Listen to the created event.
     * @param \Aviator\Helpdesk\Models\TeamAssignment $observed
     * @return void
     */
    public function created(TeamAssignment $observed)
    {
        $this->createAction('assigned to team', $observed);

        foreach ($observed->team->teamLeads as $teamLead) {
            $this->sendNotification(
               $observed,
               $teamLead,
               'assignedToTeam'
            );
       }
    }
}
