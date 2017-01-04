<?php

namespace Aviator\Helpdesk\Tests\Seeders;

use Aviator\Helpdesk\Database\Seeders\HelpdeskSeeder;
use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Tests\TestCase;
use Aviator\Helpdesk\Tests\User;

class HelpdeskSeederTest extends TestCase {

    /**
     * @group seeders
     * @test
     */
    public function it_creates_tickets()
    {
        $this->seed(HelpdeskSeeder::class);

        $seededTickets = Ticket::all();

        $this->assertEquals(52, $seededTickets->count());
    }

    /**
     * @group seeders
     * @test
     */
    public function it_assigns_half_the_tickets_to_pools()
    {
        $this->seed(HelpdeskSeeder::class);

        $assignedTickets = Ticket::assigned()->get();

        $this->assertEquals(26, $assignedTickets->count());
    }

    /**
     * @group seeders
     * @test
     */
    public function it_assigns_half_the_remaining_unassigned_tickets_to_assignment_pools()
    {
        $this->seed(HelpdeskSeeder::class);

        $tickets = Ticket::pooled()->get();

        $this->assertEquals(13, $tickets->count());
    }
}