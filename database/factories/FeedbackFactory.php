<?php

use Faker\Generator as Faker;
use App\Entities\Feedback;

$factory->define(Feedback::class, function (Faker $faker) {
    static $index=1;
    return [
        'title'     => 'feedback_' . $index++,
        'content'   => $faker->realText(50, 3),
        'email'   => $faker->email,
    ];
});
