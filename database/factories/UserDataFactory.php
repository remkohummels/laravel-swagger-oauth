<?php

use App\Models\UserData;
use Faker\Generator as Faker;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(
    App\Models\UserData::class,
    function (Faker $faker) {
        return [
            UserData::NAME => $faker->word,
            $faker->word => $faker->word,
            $faker->word => $faker->word,
            'seed' => true,
        ];
    }
);
