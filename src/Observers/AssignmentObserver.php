<?php

namespace Aviator\Helpdesk\Observers;

use Aviator\Helpdesk\Models\Action;
use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Models\Assignment;
use Illuminate\Support\Facades\Notification;

class AssignmentObserver
{
    /**
     * Listen to the created event.
     *
     * @param  DueDate
     * @return void
     */
    public function created(Assignment $observed)
    {
        $this->createAction($observed);
        $this->sendNotification($observed);
    }

    /**
     * Create the action.
     * @param  Assignment $observed
     * @return void
     */
    protected function createAction(Assignment $observed)
    {
        $action = new Action;

        $action->name = 'Assigned';
        $action->subject_id = $observed->ticket_id;
        $action->subject_type = Ticket::class;
        $action->object_id = $observed->id;
        $action->object_type = Assignment::class;
        $action->save();
    }

    /**
     * Send the notification.
     * @param  Assignment $assignment
     * @return void
     */
    protected function sendNotification(Assignment $assignment)
    {
        $notification = config('helpdesk.notifications.internal.assignedToAgent.class');

        Notification::send($assignment->assignee->user, new $notification($assignment->ticket));
    }
}
