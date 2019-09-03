<?php

namespace Aviator\Helpdesk;

use Aviator\Helpdesk\Models\Note;
use Aviator\Helpdesk\Models\Reply;
use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Models\Closing;
use Aviator\Helpdesk\Models\DueDate;
use Aviator\Helpdesk\Models\Opening;
use Aviator\Helpdesk\Models\Assignment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Aviator\Helpdesk\Models\Collaborator;
use Aviator\Helpdesk\Models\TeamAssignment;
use Aviator\Helpdesk\Observers\NoteObserver;
use Aviator\Helpdesk\Observers\ReplyObserver;
use Aviator\Helpdesk\Observers\TicketObserver;
use Aviator\Helpdesk\Observers\ClosingObserver;
use Aviator\Helpdesk\Observers\DueDateObserver;
use Aviator\Helpdesk\Observers\OpeningObserver;
use Aviator\Helpdesk\Observers\AssignmentObserver;
use Aviator\Helpdesk\Observers\CollaboratorObserver;
use Aviator\Helpdesk\Observers\TeamAssignmentObserver;

class ObserversProvider extends ServiceProvider
{
    /** @var array */
    private $observers = [
        Ticket::class => TicketObserver::class,
        Assignment::class => AssignmentObserver::class,
        DueDate::class => DueDateObserver::class,
        Reply::class => ReplyObserver::class,
        TeamAssignment::class => TeamAssignmentObserver::class,
        Closing::class => ClosingObserver::class,
        Opening::class => OpeningObserver::class,
        Note::class => NoteObserver::class,
        Collaborator::class => CollaboratorObserver::class,
    ];

    /**
     * Bootstrap the application services.
     */
    public function boot ()
    {
        /** @var Model $model */
        foreach ($this->observers as $model => $observer) {
            $model::observe($observer);
        }
    }
}
