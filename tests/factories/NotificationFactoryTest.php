<?php

namespace Aviator\Helpdesk\Tests\factories;

use Aviator\Helpdesk\Tests\TestCase;
use Illuminate\Notifications\Notification;
use Aviator\Helpdesk\Factories\DefinitionNotFound;
use Aviator\Helpdesk\Interfaces\NotificationFactoryInterface;

class NotificationFactoryTest extends TestCase
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
