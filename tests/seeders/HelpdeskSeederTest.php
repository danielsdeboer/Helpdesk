<?php

namespace Aviator\Helpdesk\Tests\Seeders;

use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Tests\TestCase;
use Aviator\Helpdesk\Database\Seeds\HelpdeskSeeder;

class HelpdeskSeederTest extends TestCase
{
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
    public function it_assigns_half_the_tickets_to_teams()
    {
        $this->seed(HelpdeskSeeder::class);

        $assignedTickets = Ticket::assigned()->get();

        $this->assertGreaterThan(1, $assignedTickets->count());
    }

    /**
     * @group seeders
     * @test
     */
    public function it_assigns_half_the_remaining_unassigned_tickets_to_assignment_teams()
    {
        $this->seed(HelpdeskSeeder::class);

        $tickets = Ticket::teamed()->get();

        $this->assertGreaterThan(1, $tickets->count());
    }

    /**
     * @group seeders
     * @test
     */
    public function it_adds_a_due_date_for_assigned_and_teamed_tickets()
    {
        $this->seed(HelpdeskSeeder::class);

        $tickets = Ticket::has('dueDate')->get();

        $this->assertEquals(39, $tickets->count());
    }

    /**
     * @group seeders
     * @test
     */
    public function it_adds_a_reply_to_a_subset_of_assigned_tickets()
    {
        $this->seed(HelpdeskSeeder::class);

        $tickets = Ticket::has('internalReplies')->get();

        $this->assertEquals(5, $tickets->count());
    }

    /**
     * @group seeders
     * @test
     */
    public function it_closes_some_random_tickets()
    {
        $this->seed(HelpdeskSeeder::class);

        $tickets = Ticket::has('closings')->get();

        $this->assertEquals(10, $tickets->count());
    }
}
