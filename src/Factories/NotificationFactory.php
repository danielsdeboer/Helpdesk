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
    private $classMap = [];

    /**
     * Constructor.
     * @param array $classMap
     */
    public function __construct(array $classMap)
    {
        $this->classMap = $classMap;
    }

    /**
     * @param string $classKey
     * @param Ticket $ticket
     * @return Notification
     * @throws DefinitionNotFound
     */
    public function make (string $classKey, Ticket $ticket) : Notification
    {
        try {
            return new $this->classMap[$classKey]($ticket);
        } catch (Exception $e) {
            $this->throwDefinitionException($classKey);
        }
    }

    /**
     * @param string $classKey
     * @throws DefinitionNotFound
     */
    private function throwDefinitionException (string $classKey)
    {
        throw new DefinitionNotFound('The definition "' . $classKey . '" was not found in the notification factory.');
    }
}
