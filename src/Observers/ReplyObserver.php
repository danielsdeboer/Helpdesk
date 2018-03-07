<?php

namespace Aviator\Helpdesk\Observers;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Reply;
use Aviator\Helpdesk\Models\Action;
use Aviator\Helpdesk\Models\Ticket;
use Illuminate\Support\Facades\Notification;
use Aviator\Helpdesk\Observers\Abstracts\AbstractObserver;

class ReplyObserver extends AbstractObserver
{
    /**
     * Listen to the created event.
     *
     * @param  Reply $observed
     * @return void
     */
    public function created(Reply $observed)
    {
        $this->createAction('reply added', $observed);
        $this->sendUserNotification($observed);
        $this->sendAgentNotification($observed);
    }

    /**
     * @param Reply $reply
     * @return void
     */
    private function sendUserNotification (Reply $reply)
    {
        if ($reply->agent && isset($reply->ticket->user)) {
            Notification::send(
                $reply->ticket->user,
                $this->factory->make('agentReplied', $reply->ticket)
            );
        }
    }

    /**
     * @param Reply $reply
     * @return void
     */
    private function sendAgentNotification (Reply $reply)
    {
        if ($reply->user && isset($reply->ticket->assignment->assignee)) {
            Notification::send(
                $reply->ticket->assignment->assignee,
                $this->factory->make('userReplied', $reply->ticket)
            );
        }
    }
}
