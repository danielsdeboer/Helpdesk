<?php

namespace Aviator\Helpdesk\Observers;

use Aviator\Helpdesk\Models\Reply;
use Aviator\Helpdesk\Observers\Abstracts\AbstractObserver;

class ReplyObserver extends AbstractObserver
{
    /**
     * Listen to the created event.
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

        if (isset($observed->ticket->user->email)) {
            if (!in_array($observed->ticket->user->email, config('helpdesk.ignored'))) {
                if ($observed->user) {
                    $this->sendNotification(
                        $observed,
                        'ticket.assignment.assignee',
                        'userReplied'
                    );
                }
            }
        }
    }
}
