<?php

namespace Aviator\Helpdesk\Factories;

use Aviator\Helpdesk\Interfaces\NotificationFactoryInterface;
use Aviator\Helpdesk\Models\Ticket;
use Aviator\Makeable\Traits\MakeableTrait;
use Exception;
use Illuminate\Notifications\Notification;

class NotificationFactory implements NotificationFactoryInterface
{
    use MakeableTrait;

    /** @var array */
    private $config = [];

    /** @var string */
    private $className;

    /**
     * Constructor.
     */
    public function __construct(string $className, array $config)
    {
        $this->className = $className;

        foreach ($config as $name => $field) {
            $this->config[$name] = array_merge($field, config('helpdesk.from'));
        }
    }

    /**
     * @throws DefinitionNotFound
     */
    public function make(string $name, Ticket $ticket): Notification
    {
        try {
            return new $this->className($ticket, $this->config[$name]);
        } catch (Exception $e) {
            $this->throwDefinitionException($name);
        }
    }

    /**
     * @throws DefinitionNotFound
     */
    private function throwDefinitionException(string $name)
    {
        throw new DefinitionNotFound('The definition "' . $name . '" was not found in the notification factory.');
    }
}
