<?php

namespace Aviator\Helpdesk\Tests\Factories;

use Aviator\Helpdesk\Factories\DefinitionNotFound;
use Aviator\Helpdesk\Interfaces\NotificationFactoryInterface;
use Aviator\Helpdesk\Tests\BKTestCase;
use Illuminate\Notifications\Notification;

class NotificationFactoryTest extends BKTestCase
{
    /** @test */
    public function it_produces_a_new_notification_instance ()
    {
        $factory = app(NotificationFactoryInterface::class);
        $notification = $factory->make('agentReplied', $this->make->ticket);

        $this->assertInstanceOf(Notification::class, $notification);

        $fromFunction = notification()->make('userReplied', $this->make->ticket);
        $this->assertInstanceOf(Notification::class, $fromFunction);
    }

    /** @test */
    public function it_throws_an_exception_if_the_class_cannot_be_created ()
    {
        $this->expectException(DefinitionNotFound::class);

        $factory = app(NotificationFactoryInterface::class);
        $factory->make('aBunchOfGarbage', $this->make->ticket);
    }
}
