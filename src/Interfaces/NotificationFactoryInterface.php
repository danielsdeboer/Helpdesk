<?php
/**
 * Created by PhpStorm.
 * User: Daniel Deboer
 * Date: 3/6/2018
 * Time: 1:01 PM
 */

namespace Aviator\Helpdesk\Interfaces;

use Aviator\Helpdesk\Models\Ticket;
use Illuminate\Notifications\Notification;

interface NotificationFactoryInterface
{
    public static function make (string $notification, Ticket $ticket) : NotificationFactoryInterface;

    public function produce () : Notification;

    public function internal () : NotificationFactoryInterface;

    public function external () : NotificationFactoryInterface;
}