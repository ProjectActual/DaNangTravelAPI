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
        factory(User::class, 5)->create();
        $this->seederAdmin();
    }

    public function seederAdmin()
    {
        $faker = Faker::create();
        foreach (range(1, 2) as $index) {
            DB::table('users')->insert([
                'email'          => 'admin_' . $index . '@gmail.com',
                'password'       => bcrypt('123123123'),
                'remember_token' => str_random(10),
                'first_name'     => $faker->firstName,
                'last_name'      => $faker->lastName,
                'active'         => 'ACTIVE',
                'phone'          => $faker->e164PhoneNumber,
                'birthday'       => $faker->date('Y-m-d', '2009-01-01'),
                'gender'         => rand(1, 2) == 1 ? User::GENDER['MALE'] : User::GENDER['FEMALE'],
            ]);
        }

        DB::table('users')->insert([
            'email'          => 'trantruongquy2702@gmail.com',
            'password'       => bcrypt('123123123'),
            'remember_token' => str_random(10),
            'first_name'     => 'Quy',
            'active'         => 'ACTIVE',
            'last_name'      => 'tran truong',
            'phone'          => '0934890911',
            'birthday'       => '1996-02-27',
            'gender'         => 'MALE',
        ]);
    }
}
