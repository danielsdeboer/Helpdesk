<?php

namespace Aviator\Helpdesk\Observers;

use Aviator\Helpdesk\Models\Action;
use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Models\Collaborator;
use Illuminate\Support\Facades\Notification;
use Aviator\Helpdesk\Observers\Abstracts\AbstractObserver;

class CollaboratorObserver extends AbstractObserver
{
    /**
     * Listen to the created event.
     * @param \Aviator\Helpdesk\Models\Collaborator $observed
     * @return void
     */
    public function created(Collaborator $observed)
    {
        $this->createAction('collaborator added', $observed);

        if (isset($observed->agent->user)) {
            $this->sendNotification(
                $observed,
                $observed->agent->user,
                'collaborator'
            );
        }
    }
}
