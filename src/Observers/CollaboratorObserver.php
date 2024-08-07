<?php

namespace Aviator\Helpdesk\Observers;

use Aviator\Helpdesk\Models\Collaborator;
use Aviator\Helpdesk\Observers\Abstracts\AbstractObserver;

class CollaboratorObserver extends AbstractObserver
{
    /**
     * Listen to the created event.
     *
     * @return void
     */
    public function created(Collaborator $observed)
    {
        $this->createAction('collaborator added', $observed);

        $this->sendNotification(
            $observed,
            'agent.user',
            'collaborator'
        );
    }
}
