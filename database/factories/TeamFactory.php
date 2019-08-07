<?php

use Faker\Generator as Faker;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(
    App\Models\Team::class,
    function (Faker $faker) {
        return [
            'name' => $faker->words(2, true),
        ];
    }
);
