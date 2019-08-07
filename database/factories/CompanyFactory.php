<?php

use App\Models\Company;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(
    App\Models\Company::class,
    function (Faker $faker) {
        return [
            Company::ID => (string) Str::orderedUuid(),
            Company::NAME => $faker->company,
            Company::ADDRESS => $faker->address,
            Company::ZIP => $faker->numberBetween(1000, 10000),
            Company::PHONE => $faker->phoneNumber,
            Company::FAX => $faker->phoneNumber,
            Company::WEBSITE => Str::substr($faker->url, 0, 64),
            Company::LANGUAGE => $faker->languageCode,
            Company::LOCATION => $faker->latitude . ',' . $faker->longitude,
            Company::DESCRIPTION => $faker->paragraph(10),
            Company::SHORT_DESCRIPTION => $faker->paragraph(3),
            Company::OPENED_24_HOURS => $faker->boolean,
            Company::PAYMENT_METHOD => $faker->randomElement(['Credit cards', 'Cash']),
            Company::FACEBOOK_URL => Str::substr($faker->url, 0, 64),
            Company::KEY_PERSON_NAME => $faker->name,
            Company::KEY_PERSON_TITLE => $faker->title,
            Company::KEY_PERSON_PHONE => $faker->phoneNumber,
            Company::KEY_PERSON_EMAIL => $faker->email,
            Company::ELIGIBILITY_REQUIREMENT => ($faker->boolean)? $faker->paragraph(4): null,
        ];
    }
);
