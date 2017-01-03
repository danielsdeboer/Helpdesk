<?php

use Aviator\Helpdesk\Models\GenericContent;
use Aviator\Helpdesk\Models\Ticket;

/**
 * User factory facilities
 */

$factory->define(config('helpdesk.userModel'), function (Faker\Generator $faker) {
    return [
        'email' => $faker->email,
    ];
});

/**
 * Helpdesk factory facilities
 */

$factory->define(Ticket::class, function (Faker\Generator $faker) {
    return [
        'user_id' => User::orderByRaw('RAND()')->first()->id,
        'name' => $faker->sentence(2),
        'content_id' => factory(GenericContent::class)->create()->id,
        'content_type' => 'Aviator\Helpdesk\Models\GenericContent',
    ];
});

$factory->define(GenericContent::class, function (Faker\Generator $faker) {
    return [
        'title' => $faker->sentence(7, true),
        'body' => $faker->paragraph(4, true),
        'attachment' => null,
    ];
});