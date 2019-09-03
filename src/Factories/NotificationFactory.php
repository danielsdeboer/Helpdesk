<?php

namespace Aviator\Helpdesk\Factories;

use Exception;
use Aviator\Helpdesk\Models\Ticket;
use Aviator\Makeable\Traits\MakeableTrait;
use Illuminate\Notifications\Notification;
use Aviator\Helpdesk\Interfaces\NotificationFactoryInterface;

class NotificationFactory implements NotificationFactoryInterface
{
    use MakeableTrait;

    /** @var array */
    private $config = [];

    /** @var string */
    private $className;

    /**
     * Constructor.
     * @param string $className
     * @param array $config
     */
    public function __construct(string $className, array $config)
    {
        $this->className = $className;

        foreach ($config as $name => $field) {
            $this->config[$name] = array_merge($field, config('helpdesk.from'));
        }
    }

    /**
     * @param string $name
     * @param Ticket $ticket
     * @return Notification
     * @throws DefinitionNotFound
     */
    public function make (string $name, Ticket $ticket): Notification
    {
        try {
            return new $this->className($ticket, $this->config[$name]);
        } catch (Exception $e) {
            $this->throwDefinitionException($name);
        }
    }

    /**
     * @param string $name
     * @throws DefinitionNotFound
     */
    private function throwDefinitionException (string $name)
    {
        throw new DefinitionNotFound('The definition "' . $name . '" was not found in the notification factory.');
    }
}
