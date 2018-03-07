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

        if ($observed->agent) {
           $this->sendNotification(
               $observed,
               'ticket.user',
               'agentReplied'
           );
        }

       if ($observed->user) {
           $this->sendNotification(
               $observed,
               'ticket.assignment.assignee',
               'userReplied'
           );
       }
    }
}
