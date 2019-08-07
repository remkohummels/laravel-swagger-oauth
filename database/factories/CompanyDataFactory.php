<?php

use Faker\Generator as Faker;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(
    App\Models\CompanyData::class,
    function (Faker $faker) {
        return [
            $faker->word => $faker->word,
            $faker->word => $faker->word,
            'seed' => true,
        ];
    }
);
