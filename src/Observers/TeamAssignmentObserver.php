<?php

namespace Aviator\Helpdesk\Observers;

use Aviator\Helpdesk\Models\Action;
use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Models\TeamAssignment;
use Illuminate\Support\Facades\Notification;

class TeamAssignmentObserver
{
    /**
     * Listen to the created event.
     * @param \Aviator\Helpdesk\Models\TeamAssignment $observed
     * @return void
     */
    public function created(TeamAssignment $observed)
    {
        $this->createAction($observed);
        $this->sendNotification($observed);
    }

    /**
     * Create the action.
     * @param \Aviator\Helpdesk\Models\TeamAssignment $observed
     * @return void
     */
    protected function createAction(TeamAssignment $observed)
    {
        $action = new Action;

        $action->name = 'Assigned To Team';
        $action->subject_id = $observed->ticket_id;
        $action->subject_type = Ticket::class;
        $action->object_id = $observed->id;
        $action->object_type = TeamAssignment::class;
        $action->save();
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
