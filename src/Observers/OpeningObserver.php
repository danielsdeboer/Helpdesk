<?php

namespace Aviator\Helpdesk\Observers;

use Aviator\Helpdesk\Models\Action;
use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Models\Opening;
use Illuminate\Support\Facades\Notification;
use Aviator\Helpdesk\Observers\Abstracts\AbstractObserver;

class OpeningObserver extends AbstractObserver
{
    /**
     * Listen to the created event.
     *
     * @param  Opening $observed
     * @return void
     */
    public function created(Opening $observed)
    {
        $this->createAction('opened', $observed);

//        if (isset($observed->ticket->user)) {
//            $this->sendNotification(
//                $observed,
//                $observed->ticket->user,
//                'opened'
//            );
//        }
    }
}
