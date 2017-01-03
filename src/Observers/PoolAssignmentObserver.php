<?php

namespace Aviator\Helpdesk\Observers;

use Aviator\Helpdesk\Models\Action;
use Aviator\Helpdesk\Models\PoolAssignment;
use Aviator\Helpdesk\Models\Ticket;
use Illuminate\Support\Facades\Notification;

class PoolAssignmentObserver
{
    /**
     * Listen to the created event.
     *
     * @param  DueDate
     * @return void
     */
    public function created(PoolAssignment $observed)
    {
        $this->createAction($observed);
        $this->sendNotification($observed);
    }

    /**
     * Create the action
     * @param  Assignment $observed
     * @return void
     */
    protected function createAction(PoolAssignment $observed)
    {
        $action = new Action;

        $action->name = 'Assigned To Pool';
        $action->subject_id = $observed->ticket_id;
        $action->subject_type = Ticket::class;
        $action->object_id = $observed->id;
        $action->object_type = PoolAssignment::class;
        $action->save();
    }

    /**
     * Send the notification
     * @param  Assignment $observed
     * @return void
     */
    protected function sendNotification(PoolAssignment $observed)
    {
        $notification = config('helpdesk.notifications.internal.assignedToPool.class');

        Notification::send($observed->pool->teamLead, new $notification($observed->ticket));
    }
}