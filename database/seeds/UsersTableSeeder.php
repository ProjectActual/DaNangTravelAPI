<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Entities\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::truncate();
        factory(User::class, 10)->create();
        $this->seederAdmin();
    }

    public function seederAdmin()
    {
        $faker = Faker::create();
        foreach (range(1, 5) as $index) {
            DB::table('users')->insert([
                'email'             => 'admin_' . $index . '@gmail.com',
                'password'          => bcrypt('123123123'),
                'remember_token'    => str_random(10),
                'first_name'        => $faker->firstName,
                'last_name'         => $faker->lastName,
                'phone'             => $faker->e164PhoneNumber,
                'birthday'          => $faker->date('Y-m-d', '2009-01-01'),
                'gender'            => rand(1, 2) == 1 ? User::GENDER['MALE'] : User::GENDER['FEMALE'],
            ]);
        }
    }
}