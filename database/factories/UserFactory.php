<?php

use Faker\Generator as Faker;

use App\Entities\User;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function (Faker $faker) {
    return [
        'email'             => $faker->unique()->safeEmail,
        'password'          => bcrypt('123123123'),
        'remember_token'    => str_random(10),
        'first_name'        => $faker->firstName,
        'last_name'         => $faker->lastName,
    ];
});
