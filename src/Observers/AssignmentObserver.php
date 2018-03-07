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

        $this->sendNotification(
            $observed,
            'assignee.user',
            'assignedToAgent'
        );
    }
}
