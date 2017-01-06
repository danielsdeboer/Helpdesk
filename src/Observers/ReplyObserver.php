<?php

namespace Aviator\Helpdesk\Observers;

use Aviator\Helpdesk\Models\Action;
use Aviator\Helpdesk\Models\Reply;
use Aviator\Helpdesk\Models\Ticket;
use Illuminate\Support\Facades\Notification;

class ReplyObserver
{
    /**
     * Listen to the created event.
     *
     * @param  Reply $observed
     * @return void
     */
    public function created(Reply $observed)
    {
        $this->createAction($observed);
        $this->sendNotification($observed);
    }

    /**
     * Create the action
     * @param  Reply  $observed
     * @return void
     */
    protected function createAction($observed)
    {
        $action = new Action;

        $action->name = 'Reply Added';
        $action->subject_id = $observed->ticket_id;
        $action->subject_type = Ticket::class;
        $action->object_id = $observed->id;
        $action->object_type = Reply::class;
        $action->save();
    }

    /**
     * Send the notification to the user if the reply is placed by an agent
     * and vice versa.
     * @param  Reply $observed
     * @return void
     */
    protected function sendNotification($observed)
    {
        if ($observed->user_id && $observed->ticket->assignment) {
            $notification = config('helpdesk.notifications.internal.replied.class');

            Notification::send($observed->ticket->assignment->assignee, new $notification($observed->ticket));
        }

        if ($observed->agent_id) {
            $notification = config('helpdesk.notifications.external.replied.class');

            Notification::send($observed->ticket->user, new $notification($observed->ticket));
        }
    }
}