<?php

namespace Aviator\Helpdesk\Observers;

use Aviator\Helpdesk\Models\Action;
use Aviator\Helpdesk\Models\InternalReply;
use Aviator\Helpdesk\Models\Ticket;
use Illuminate\Support\Facades\Notification;

class InternalReplyObserver
{
    /**
     * Listen to the created event.
     *
     * @param  InternalReply $observed
     * @return void
     */
    public function created(InternalReply $observed)
    {
        $this->createAction($observed);
        $this->sendNotification($observed);
    }

    /**
     * Create the action
     * @param  InternalReply  $observed
     * @return void
     */
    protected function createAction(InternalReply $observed)
    {
        $action = new Action;

        $action->name = 'Internal Reply Added';
        $action->subject_id = $observed->ticket_id;
        $action->subject_type = Ticket::class;
        $action->object_id = $observed->id;
        $action->object_type = InternalReply::class;
        $action->save();
    }

    /**
     * Send the notification
     * @param  InternalReply $observed
     * @return void
     */
    protected function sendNotification(InternalReply $observed)
    {
        $notification = config('helpdesk.notifications.external.replied.class');

        Notification::send($observed->ticket->user, new $notification($observed->ticket));
    }
}