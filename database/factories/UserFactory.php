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
    static $index = 1;
    return [
        'email'             => 'congtacvien_' . $index++ . '@gmail.com',
        'password'          => bcrypt('123123123'),
        'remember_token'    => str_random(10),
        'first_name'        => $faker->firstName,
        'last_name'         => $faker->lastName,
        'phone'             => $faker->e164PhoneNumber,
        'active'            => 'YES',
        'birthday'          => $faker->date('Y-m-d', '2009-01-01'),
        'gender'            => rand(1, 2) == 1 ? User::GENDER['MALE'] : User::GENDER['FEMALE'],
    ];
});
