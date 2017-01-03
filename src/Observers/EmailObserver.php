<?php

namespace Aviator\Helpdesk\Observers;

use Aviator\Helpdesk\Models\Action;
use Aviator\Helpdesk\Models\Email;
use Aviator\Helpdesk\Models\Ticket;

class EmailObserver
{
    /**
     * Listen to the created event.
     *
     * @param  DueDate
     * @return void
     */
    public function created(Email $observed)
    {
        $action = new Action;

        $action->name = 'Emailed';
        $action->subject_id = $observed->ticket_id;
        $action->subject_type = Ticket::class;
        $action->object_id = $observed->id;
        $action->object_type = Email::class;
        $action->save();
    }
}