<?php

namespace Aviator\Helpdesk\Observers;

use Aviator\Helpdesk\Models\Action;
use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Models\Assignment;
use Illuminate\Support\Facades\Notification;
use Aviator\Helpdesk\Observers\Abstracts\AbstractObserver;

class AssignmentObserver extends AbstractObserver
{
    /**
     * Listen to the created event.
     * @param \Aviator\Helpdesk\Models\Assignment $observed
     * @return void
     */
    public function created(Assignment $observed)
    {
        $this->createAction('assigned', $observed);
        $this->sendNotification($observed);
    }

    /**
     * Send the notification.
     * @param  Assignment $assignment
     * @return void
     */
    protected function sendNotification(Assignment $assignment)
    {
        $notification = config('helpdesk.notifications.internal.assignedToAgent.class');

        if (isset($assignment->assignee->user)) {
            Notification::send(
                $assignment->assignee->user,
                new $notification($assignment->ticket)
            );
        }
    }
}
