<?php
/**
 * Created by PhpStorm.
 * User: Daniel Deboer
 * Date: 3/6/2018
 * Time: 1:08 PM
 */

namespace Aviator\Helpdesk\Factories;

use Aviator\Helpdesk\Interfaces\NotificationFactoryInterface;
use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Notifications\External\Closed;
use Aviator\Helpdesk\Notifications\External\Opened;
use Aviator\Helpdesk\Notifications\External\Replied;
use Aviator\Helpdesk\Notifications\Internal\AssignedToAgent;
use Aviator\Helpdesk\Notifications\Internal\AssignedToTeam;
use Aviator\Helpdesk\Notifications\Internal\Collaborator;
use Aviator\Helpdesk\Notifications\Internal\Replied as InternalReplied;
use Exception;
use Illuminate\Notifications\Notification;

class NotificationFactory implements NotificationFactoryInterface
{
    /** @var array */
    private $external = [
        'closed' => Closed::class,
        'opened' => Opened::class,
        'replied' => Replied::class,
    ];

    /** @var array */
    private $internal = [
        'assignedToAgent' => AssignedToAgent::class,
        'assignedToTeam' => AssignedToTeam::class,
        'collaborator' => Collaborator::class,
        'replied' => InternalReplied::class,
    ];

    /** @var bool */
    private $isInternal = true;

    /**
     * @var string
     */
    private $notification;

    /**
     * @var Ticket
     */
    private $ticket;

    /**
     * Static constructor.
     * @param string $notification
     * @param Ticket $ticket
     * @return NotificationFactoryInterface
     */
    public static function make (string $notification, Ticket $ticket) : NotificationFactoryInterface
    {
        return new static($notification, $ticket);
    }

    /**
     * @param string $notification
     * @param Ticket $ticket
     */
    public function __construct (string $notification, Ticket $ticket)
    {
        $this->notification = $notification;
        $this->ticket = $ticket;
    }

    /**
     * @return NotificationFactoryInterface
     */
    public function internal () : NotificationFactoryInterface
    {
        $this->isInternal = true;

        return $this;
    }

    /**
     * @return NotificationFactoryInterface
     */
    public function external () : NotificationFactoryInterface
    {
        $this->isInternal = false;

        return $this;
    }

    /**
     * @return Notification
     * @throws DefinitionNotFound
     */
    public function produce () : Notification
    {
        if ($this->isInternal) {
            return $this->of('internal');
        }

        return $this->of('external');
    }

    /**
     * @param string $property
     * @return Notification
     * @throws DefinitionNotFound
     */
    private function of (string $property) : Notification
    {
        try {
            return new $this->{$property}[$this->notification]($this->ticket);
        } catch (Exception $e) {
            throw new DefinitionNotFound('The definition "' . $this->notification . '" was not found in the notification factory.');
        }
    }
}