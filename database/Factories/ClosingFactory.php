<?php

namespace Aviator\Helpdesk\Database\Factories;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Closing;
use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Tests\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClosingFactory extends Factory
{
    protected $model = Closing::class;

    public function definition(): array
    {
        return [
            'ticket_id' => Ticket::factory()->state(['status' => 'closed']),
            'note' => 'Test note',
            'agent_id' => Agent::factory(),
            'is_visible' => true,
        ];
    }

    public function isUser(): static
    {
        return $this->state(fn () => [
            'user_id' => User::factory(),
            'is_visible' => true,
        ]);
    }
}
