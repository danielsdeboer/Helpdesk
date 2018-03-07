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
        $this->sendNotification($observed);
    }

    /**
     * Send the notification.
     * @param  \Aviator\Helpdesk\Models\TeamAssignment $observed
     * @return void
     */
    protected function sendNotification(TeamAssignment $observed)
    {
        $notification = config('helpdesk.notifications.internal.assignedToTeam.class');

        Notification::send(
            $observed->team->teamLeads,
            new $notification($observed->ticket)
        );
    }
}
