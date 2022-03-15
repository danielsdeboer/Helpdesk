<?php

namespace Aviator\Helpdesk\Tests\Feature\Factories;

use Aviator\Helpdesk\Factories\DefinitionNotFound;
use Aviator\Helpdesk\Interfaces\NotificationFactoryInterface;
use Aviator\Helpdesk\Tests\BKTestCase;
use Illuminate\Notifications\Notification;
use function notification;
use function resolve;

class NotificationFactoryTest extends BKTestCase
{
    private NotificationFactoryInterface $notificationFactory;

    public function setUp (): void
    {
        parent::setUp();

        $this->notificationFactory = resolve(
            NotificationFactoryInterface::class,
        );
    }

    /** @test */
    public function it_produces_a_new_notification_instance ()
    {
        $notification = $this->notificationFactory->make(
            'agentReplied',
            $this->make->ticket,
        );

        $this->assertInstanceOf(Notification::class, $notification);

        $fromFunction = notification()->make(
            'userReplied',
            $this->make->ticket,
        );

        $this->assertInstanceOf(Notification::class, $fromFunction);
    }

    /** @test */
    public function it_throws_an_exception_if_the_class_cannot_be_created ()
    {
        $this->expectException(DefinitionNotFound::class);

        $this->notificationFactory->make(
            'aBunchOfGarbage',
            $this->make->ticket,
        );
    }
}
