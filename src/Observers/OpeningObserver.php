<?php

namespace Aviator\Helpdesk\Observers;

use Aviator\Helpdesk\Models\Action;
use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Models\Opening;
use Illuminate\Support\Facades\Notification;

class OpeningObserver
{
    /**
     * Listen to the created event.
     *
     * @param  Opening $observed
     * @return void
     */
    public function created(Opening $observed)
    {
        $this->createAction($observed);
        $this->sendNotification($observed);
    }

    /**
     * Create the action.
     * @param  Opening  $observed
     * @return void
     */
    protected function createAction(Opening $observed)
    {
        $action = new Action;

        $action->name = 'Opened';
        $action->subject_id = $observed->ticket_id;
        $action->subject_type = Ticket::class;
        $action->object_id = $observed->id;
        $action->object_type = Opening::class;
        $action->save();
    }

    /**
     * Send the notification.
     * @param  Opening $observed
     * @return void
     */
    protected function sendNotification(Opening $observed)
    {
        $notification = config('helpdesk.notifications.external.opened.class');

        if (isset($observed->ticket->user)) {
            Notification::send($observed->ticket->user, new $notification($observed->ticket));
        }
    }
}
