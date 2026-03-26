<?php

namespace Aviator\Helpdesk\Database\Factories;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Reply;
use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Tests\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReplyFactory extends Factory
{
    protected $model = Reply::class;

    public function definition(): array
    {
        return [
            'ticket_id' => Ticket::factory(),
            'body' => fake()->paragraph(2),
            'agent_id' => Agent::factory(),
            'user_id' => null,
            'is_visible' => true,
        ];
    }

    public function isUser(): static
    {
        return $this->state(fn () => [
            'agent_id' => null,
            'user_id' => User::factory(),
        ]);
    }
}
