<?php

namespace Aviator\Helpdesk\Database\Factories;

use Aviator\Helpdesk\Tests\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->email(),
        ];
    }

    public function isInternal(): static
    {
        return $this->state(fn () => [
            'name' => fake()->name(),
            'email' => fake()->email(),
            'is_internal' => 1,
        ]);
    }
}
