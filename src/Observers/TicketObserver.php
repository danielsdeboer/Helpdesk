<?php

namespace Aviator\Helpdesk\Observers;

use Aviator\Helpdesk\Models\Action;
use Aviator\Helpdesk\Models\Ticket;

class TicketObserver
{
    /**
     * Listen to the created event.
     *
     * @param  Ticket
     * @return void
     */
    public function created(Ticket $ticket)
    {
        $this->createAction($ticket);
        $this->sendNotification($ticket);
    }

    /**
     * Create the action
     * @param  Ticket $ticket
     * @return void
     */
    protected function createAction(Ticket $ticket)
    {
        $action = new Action;

        $action->name = 'Created';
        $action->subject_id = $ticket->id;
        $action->subject_type = Ticket::class;
        $action->save();
    }

    /**
     * Send the notification
     * @param  Ticket $ticket
     * @return void
     */
    protected function sendNotification(Ticket $ticket)
    {
        $notification = config('helpdesk.notifications.external.created.class');

        \Notification::send($ticket->user, new $notification($ticket));
    }
}