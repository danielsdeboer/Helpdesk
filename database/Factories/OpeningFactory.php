<?php

namespace Aviator\Helpdesk\Database\Factories;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Opening;
use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Tests\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OpeningFactory extends Factory
{
    protected $model = Opening::class;

    public function definition(): array
    {
        return [
            'ticket_id' => Ticket::factory(),
            'agent_id' => null,
            'user_id' => User::factory(),
            'is_visible' => true,
        ];
    }

    public function isAgent(): static
    {
        return $this->state(fn () => [
            'agent_id' => Agent::factory(),
            'user_id' => null,
        ]);
    }
}
