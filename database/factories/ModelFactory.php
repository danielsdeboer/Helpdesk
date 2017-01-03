<?php

use Aviator\Helpdesk\Models\Assignment;
use Aviator\Helpdesk\Models\DueDate;
use Aviator\Helpdesk\Models\Email;
use Aviator\Helpdesk\Models\GenericContent;
use Aviator\Helpdesk\Models\Pool;
use Aviator\Helpdesk\Models\PoolAssignment;
use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Tests\User;
use Carbon\Carbon;

/**
 * User factory facilities
 */

$factory->define(User::class, function (Faker\Generator $faker) {
    return [
        'email' => $faker->email,
    ];
});

/**
 * Helpdesk factory facilities
 */

$factory->define(Ticket::class, function (Faker\Generator $faker) {
    return [
        'user_id' => factory(User::class)->create()->id,
        'content_id' => factory(GenericContent::class)->create()->id,
        'content_type' => 'Aviator\Helpdesk\Models\GenericContent',
    ];
});

$factory->define(GenericContent::class, function (Faker\Generator $faker) {
    return [
        'title' => $faker->sentence(2, true),
        'body' => $faker->paragraph(4, true),
    ];
});

$factory->define(Assignment::class, function (Faker\Generator $faker) {
    return [
        'ticket_id' => factory(Ticket::class)->create()->id,
        'assigned_to' => factory(User::class)->create()->id,
        'created_by' => null,
        'is_visible' => false,
    ];
});

$factory->define(DueDate::class, function (Faker\Generator $faker) {
    return [
        'ticket_id' => factory(Ticket::class)->create()->id,
        'due_on' => Carbon::parse('+1 day'),
        'created_by' => null,
        'is_visible' => false,
    ];
});

$factory->define(Email::class, function (Faker\Generator $faker) {
    return [
        'ticket_id' => factory(Ticket::class)->create()->id,
        'subject' => 'Test Subject',
        'body' => 'Test body.',
        'created_by' => null,
        'is_visible' => false,
    ];
});

$factory->define(Pool::class, function (Faker\Generator $faker) {
    return [
        'team_lead' => factory(User::class)->create()->id,
        'name' => 'Customer Service',
    ];
});

$factory->define(PoolAssignment::class, function (Faker\Generator $faker) {
    return [
        'ticket_id' => factory(Ticket::class)->create()->id,
        'pool_id' => factory(Pool::class)->create()->id,
        'created_by' => null,
        'is_visible' => false,
    ];
});