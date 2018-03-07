<?php

namespace Aviator\Helpdesk\Observers;

use Aviator\Helpdesk\Models\Closing;
use Aviator\Helpdesk\Observers\Abstracts\AbstractObserver;
use Illuminate\Support\Facades\Notification;

class ClosingObserver extends AbstractObserver
{
    /**
     * Listen to the created event.
     * @param  Closing $observed
     * @return void
     */
    public function created (Closing $observed)
    {
        $this->createAction('closed', $observed);

//        if (isset($observed->ticket->user)) {
//            $this->sendNotification(
//                $observed,
//                $observed->ticket->user,
//                'closed'
//            );
//        }
    }
}
