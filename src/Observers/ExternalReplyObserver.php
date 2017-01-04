<?php

namespace Aviator\Helpdesk\Observers;

use Aviator\Helpdesk\Models\Action;
use Aviator\Helpdesk\Models\ExternalReply;
use Aviator\Helpdesk\Models\Ticket;
use Illuminate\Support\Facades\Notification;

class ExternalReplyObserver
{
    /**
     * Listen to the created event.
     *
     * @param  ExternalReply $observed
     * @return void
     */
    public function created(ExternalReply $observed)
    {
        $this->createAction($observed);
        $this->sendNotification($observed);
    }

    /**
     * Create the action
     * @param  ExternalReply  $observed
     * @return void
     */
    protected function createAction(ExternalReply $observed)
    {
        $action = new Action;

        $action->name = 'External Reply Added';
        $action->subject_id = $observed->ticket_id;
        $action->subject_type = Ticket::class;
        $action->object_id = $observed->id;
        $action->object_type = ExternalReply::class;
        $action->save();
    }

    /**
     * Send the notification
     * @param  ExternalReply $observed
     * @return void
     */
    protected function sendNotification(ExternalReply $observed)
    {
        $notification = config('helpdesk.notifications.internal.replied.class');

        Notification::send($observed->ticket->getInternalUser(), new $notification($observed->ticket));
    }
}