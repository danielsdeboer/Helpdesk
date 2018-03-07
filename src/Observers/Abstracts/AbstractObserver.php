<?php

namespace Aviator\Helpdesk\Observers\Abstracts;

use Aviator\Helpdesk\Interfaces\NotificationFactoryInterface;
use Aviator\Helpdesk\Models\Action;
use Aviator\Helpdesk\Models\ActionBase;
use Aviator\Helpdesk\Models\Ticket;

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
        $this->action->name = ucfirst($name);
        $this->action->subject_id = $model->ticket_id;
        $this->action->subject_type = Ticket::class;
        $this->action->object_id = $model->id;
        $this->action->object_type = get_class($model);
        $this->action->save();
    }
}