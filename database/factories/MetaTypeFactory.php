<?php

use App\Models\MetaType;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(
    MetaType::class,
    function (Faker $faker) {
        return [
            'id' => (string) Str::orderedUuid(),
            'name' => $faker->words($faker->randomElement([1, 2, 3]), true),
            'is_required' => $faker->boolean,
            'is_custom' => $faker->boolean,
            'weight' => $faker->numberBetween(0, 255),
            'status' => $faker->randomElement(MetaType::getAllAvailableStatuses()),
            'type'   => $faker->randomElement(MetaType::getAllAvailableTypes()),
            'validation' => $faker->randomElement(['string', 'boolean', 'numeric']),
        ];
    }
);