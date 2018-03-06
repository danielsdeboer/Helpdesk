<?php

namespace Aviator\Helpdesk\Observers;

use Aviator\Helpdesk\Models\Reply;
use Aviator\Helpdesk\Models\Action;
use Aviator\Helpdesk\Models\Ticket;
use Illuminate\Support\Facades\Notification;
use Aviator\Helpdesk\Models\Agent;

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
        $this->sendUserNotification($observed);
        $this->sendAgentNotification($observed);
    }

    /**
     * Create the action.
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
     * @param Reply $reply
     * @return void
     */
    private function sendUserNotification (Reply $reply) 
    {

        if ($reply->agent && isset($reply->ticket->user)) {
            $notification = config('helpdesk.notifications.external.replied.class');

            Notification::send($reply->ticket->user, new $notification($reply->ticket));
        }
    }

    /**
     * @param Reply $reply
     * @return void
     */
    private function sendAgentNotification (Reply $reply) 
    {
        if ($reply->user && isset($reply->ticket->assignment->assignee)) {
            $notification = config('helpdesk.notifications.internal.replied.class');

            Notification::send($reply->ticket->assignment->assignee, new $notification($reply->ticket));
        }
    }



    /**
     * Send the notification to the user if the reply is placed by an agent
     * and vice versa.
     * @param  Reply $observed
     * @return void
     */
    protected function sendNotification(Reply $observed)
    {
        if ($observed->user_id && $observed->ticket->assignment) {
            $notification = config('helpdesk.notifications.internal.replied.class');

            Notification::send($observed->ticket->assignment->assignee, new $notification($observed->ticket));
        }

        if ($observed->agent) {
            $notification = config('helpdesk.notifications.external.replied.class');

            Notification::send($observed->ticket->user, new $notification($observed->ticket));
        }
    }
}
