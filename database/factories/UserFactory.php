<?php

use App\Models\User;
use Faker\Generator as Faker;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(
    App\Models\User::class,
    function (Faker $faker) {
        return [
            User::NAME => $faker->name,
            User::FIRST_NAME => $faker->firstName,
            User::LAST_NAME => $faker->lastName,
            User::EMAIL => $faker->email,
            User::USER_LITMOS_ID => $faker->randomNumber,
            User::OLD_USER_ID => $faker->randomNumber,
            User::WP_USER_ID => $faker->randomNumber,
            User::EMAIL_VERIFIED_AT => ($faker->boolean(90)) ? now() : null,
            User::PASSWORD => Hash::make('123456789'), // secret
            'status' => $faker->randomElement(User::getAllAvailableStatuses()),
        ];
    }
);
