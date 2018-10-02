<?php

namespace Aviator\Helpdesk\Observers;

use Aviator\Helpdesk\Models\Assignment;
use Aviator\Helpdesk\Observers\Abstracts\AbstractObserver;

class AssignmentObserver extends AbstractObserver
{
    /**
     * Listen to the created event.
     * @param Assignment $observed
     * @return void
     */
    public function created(Assignment $observed)
    {
        $this->createAction('assigned', $observed);

        if (isset($observed->ticket->user->email)) {
            if (!in_array($observed->ticket->user->email, config('helpdesk.ignored'))) {
                $this->sendNotification(
                    $observed,
                    'assignee.user',
                    'assignedToAgent'
                );
            }
        }
    }
}
