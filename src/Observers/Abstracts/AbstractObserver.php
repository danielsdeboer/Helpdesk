<?php

namespace Aviator\Helpdesk\Observers\Abstracts;

use Aviator\Helpdesk\Models\Action;
use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Models\ActionBase;
use Aviator\Helpdesk\Interfaces\NotificationFactoryInterface;

abstract class AbstractObserver
{
    /** @var NotificationFactoryInterface */
    protected $factory;

    /** @var Action */
    protected $action;

    /**
     * Constructor.
     * @param NotificationFactoryInterface $factory
     * @param Action $action
     */
    public function __construct (NotificationFactoryInterface $factory, Action $action)
    {
        $this->factory = $factory;
        $this->action = $action;
    }

    /**
     * Create the action.
     * @param string $name
     * @param ActionBase $model
     * @return void
     */
    protected function createAction (string $name, ActionBase $model)
    {
        $this->action->name = ucwords($name);
        $this->action->subject_id = $model->ticket_id;
        $this->action->subject_type = Ticket::class;
        $this->action->object_id = $model->id;
        $this->action->object_type = get_class($model);
        $this->action->save();
    }

    /**
     * @param ActionBase $model
     * @param string $notifiable Dot notated string used to access the notifiable.
     * @param string $classKey
     */
    protected function sendNotification (ActionBase $model, string $notifiable, string $classKey)
    {
        $notifiable = reduceProperties($model, $notifiable);
        
        if ($notifiable && method_exists($notifiable, 'notify')) {
            $notifiable->notify(
                $this->factory->make($classKey, $model->ticket)
            );
        }
    }
}
