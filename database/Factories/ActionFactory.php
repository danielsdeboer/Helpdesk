<?php

namespace Aviator\Helpdesk\Database\Factories;

use Aviator\Helpdesk\Models\Action;
use Aviator\Helpdesk\Models\Assignment;
use Aviator\Helpdesk\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActionFactory extends Factory
{
    protected $model = Action::class;

    public function definition(): array
    {
        return [
            'name' => 'Test Name',
            'subject_id' => Ticket::factory(),
            'subject_type' => 'Aviator\Helpdesk\Models\Ticket',
            'object_id' => Assignment::factory(),
            'object_type' => 'Aviator\Helpdesk\Models\Assignment',
        ];
    }
}
