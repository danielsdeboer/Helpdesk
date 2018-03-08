<?php

namespace Aviator\Helpdesk\Observers;

use Aviator\Helpdesk\Models\Closing;
use Aviator\Helpdesk\Observers\Abstracts\AbstractObserver;

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

        $this->sendNotification(
            $observed,
            'ticket.user',
            'closed'
        );
    }
}
