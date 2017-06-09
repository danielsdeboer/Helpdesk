<?php

namespace Aviator\Helpdesk\Observers;

use Aviator\Helpdesk\Models\Action;
use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Models\Closing;
use Illuminate\Support\Facades\Notification;

class ClosingObserver
{
    /**
     * Listen to the created event.
     *
     * @param  Closing $observed
     * @return void
     */
    public function created(Closing $observed)
    {
        $this->createAction($observed);
        $this->sendNotification($observed);
    }

    /**
     * Create the action.
     * @param  Closing  $observed
     * @return void
     */
    protected function createAction(Closing $observed)
    {
        $action = new Action;

        $action->name = 'Closed';
        $action->subject_id = $observed->ticket_id;
        $action->subject_type = Ticket::class;
        $action->object_id = $observed->id;
        $action->object_type = Closing::class;
        $action->save();
    }

    /**
     * Send the notification.
     * @param  Closing $observed
     * @return void
     */
    protected function sendNotification(Closing $observed)
    {
        $notification = config('helpdesk.notifications.external.closed.class');

        Notification::send($observed->ticket->user, new $notification($observed->ticket));
    }
}
