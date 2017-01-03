<?php

namespace Aviator\Helpdesk\Observers;

use Aviator\Helpdesk\Models\Action;
use Aviator\Helpdesk\Models\Email;
use Aviator\Helpdesk\Models\Ticket;
use Illuminate\Support\Facades\Notification;

class EmailObserver
{
    /**
     * Listen to the created event.
     *
     * @param  DueDate
     * @return void
     */
    public function created(Email $observed)
    {
        $this->createAction($observed);
        $this->sendNotification($observed);
    }

    /**
     * Create the action
     * @param  Email  $observed
     * @return void
     */
    protected function createAction(Email $observed)
    {
        $action = new Action;

        $action->name = 'Emailed';
        $action->subject_id = $observed->ticket_id;
        $action->subject_type = Ticket::class;
        $action->object_id = $observed->id;
        $action->object_type = Email::class;
        $action->save();
    }

    protected function sendNotification(Email $observed)
    {
        $notification = config('helpdesk.notifications.external.emailed.class');

        Notification::send($observed->ticket->user, new $notification($observed->ticket));
    }
}