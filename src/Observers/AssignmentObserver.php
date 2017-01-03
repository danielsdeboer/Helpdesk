<?php

namespace Aviator\Helpdesk\Observers;

use Aviator\Helpdesk\Models\Action;
use Aviator\Helpdesk\Models\Assignment;
use Aviator\Helpdesk\Models\Ticket;

class AssignmentObserver
{
    /**
     * Listen to the created event.
     *
     * @param  DueDate
     * @return void
     */
    public function created(Assignment $observed)
    {
        $action = new Action;

        $action->name = 'Assigned';
        $action->subject_id = $observed->ticket_id;
        $action->subject_type = Ticket::class;
        $action->object_id = $observed->id;
        $action->object_type = Assignment::class;
        $action->save();
    }
}