<?php

namespace Aviator\Helpdesk\Database\Factories;

use Aviator\Helpdesk\Models\GenericContent;
use Illuminate\Database\Eloquent\Factories\Factory;

class GenericContentFactory extends Factory
{
    protected $model = GenericContent::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(2, true),
            'body' => fake()->paragraph(4, true),
        ];
    }
}
