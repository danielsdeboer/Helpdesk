<?php

namespace Aviator\Helpdesk\Observers;

use Aviator\Helpdesk\Models\Action;
use Aviator\Helpdesk\Models\Collaborator;
use Aviator\Helpdesk\Models\Ticket;
use Illuminate\Support\Facades\Notification;

class CollaboratorObserver
{
    /**
     * Listen to the created event.
     * @param \Aviator\Helpdesk\Models\Collaborator $observed
     * @return void
     */
    public function created(Collaborator $observed)
    {
        $this->createAction($observed);
        $this->sendNotification($observed);
    }

    /**
     * Create the action.
     * @param \Aviator\Helpdesk\Models\Collaborator $observed
     * @return void
     */
    protected function createAction(Collaborator $observed)
    {
        $action = new Action;

        $action->name = 'Added';
        $action->subject_id = $observed->ticket_id;
        $action->subject_type = Ticket::class;
        $action->object_id = $observed->id;
        $action->object_type = Collaborator::class;
        $action->save();
    }

    /**
     * Send the notification.
     * @param \Aviator\Helpdesk\Models\Collaborator $collaborator
     * @return void
     */
    protected function sendNotification(Collaborator $collaborator)
    {
        $notification = config('helpdesk.notifications.internal.collaborator.class');

        Notification::send(
            $collaborator->agent->user,
            new $notification($collaborator->ticket)
        );
    }
}
