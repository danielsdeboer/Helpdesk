<?php

namespace Aviator\Helpdesk\Database\Factories;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Tests\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AgentFactory extends Factory
{
    protected $model = Agent::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'is_super' => 0,
        ];
    }

    public function isSuper(): static
    {
        return $this->state(fn () => [
            'is_super' => 1,
        ]);
    }
}
