<?php

namespace Aviator\Helpdesk\Observers;

use Aviator\Helpdesk\Models\Action;
use Aviator\Helpdesk\Models\DueDate;
use Aviator\Helpdesk\Models\Ticket;

class DueDateObserver
{
    /**
     * Listen to the created event.
     *
     * @param  DueDate
     * @return void
     */
    public function created(DueDate $observed)
    {
        $action = new Action;

        $action->name = 'Due Dated';
        $action->subject_id = $observed->ticket_id;
        $action->subject_type = Ticket::class;
        $action->object_id = $observed->id;
        $action->object_type = DueDate::class;
        $action->save();
    }
}