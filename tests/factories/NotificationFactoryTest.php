<?php

namespace Aviator\Helpdesk\Tests\factories;

use Aviator\Helpdesk\Factories\DefinitionNotFound;
use Aviator\Helpdesk\Factories\NotificationFactory;
use Aviator\Helpdesk\Notifications\Internal\Replied;
use Aviator\Helpdesk\Notifications\External\Replied as ExternalReplied;
use Aviator\Helpdesk\Tests\TestCase;
use Illuminate\Notifications\Notification;

class NotificationFactoryTest extends TestCase
{
    /** @test */
    public function it_produces_a_new_notification_instance ()
    {
        $factory = NotificationFactory::make('replied', $this->make->ticket)->produce();

        $this->assertInstanceOf(Notification::class, $factory);
    }

    /** @test */
    public function it_throws_an_exception_if_the_definition_doesnt_exist ()
    {
        $this->expectException(DefinitionNotFound::class);

        NotificationFactory::make('somethingThatDoesntExist', $this->make->ticket)
            ->produce();
    }

    /** @test */
    public function it_can_specify_external_or_internal_notifications_defaulting_to_internal ()
    {
        $notification = NotificationFactory::make('replied', $this->make->ticket)
            ->produce();

        $this->assertInstanceOf(Replied::class, $notification);

        $notification = NotificationFactory::make('replied', $this->make->ticket)
            ->external()
            ->produce();

        $this->assertInstanceOf(ExternalReplied::class, $notification);
    }
}