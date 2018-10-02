<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

use App\Entities\Category;
use App\Entities\Post;
use App\Entities\User;

class PostsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Post::truncate();

        $this->data();
    }

    public function data()
    {
        $faker = Faker::create();

        $count_user = User::all()->count();

        Category::all()->each(function ($category) use ($faker, $count_user) {
            foreach (range(1, rand(10, 30)) as $index) {
                $category->posts()->create([
                    'category_id'   => $category->id,
                    'title'         => 'category_' . $category->name_category . ' - post_' . $index,
                    'uri_post'      => 'category-' . $category->uri_category . '-post-' . $index,
                    'content'       => $faker->realText(200, 3),
                    'summary'       => $faker->realText(120, 3),
                    'avatar_post'        => '/images/users/default-avatar.png',
                    'status'        => (rand(1,2) == 1) ? Post::STATUS['ACTIVE'] : Post::STATUS['INACTIVE'],
                    'user_id'       => rand(1, $count_user),
                    'created_at'    => now(),
                    'updated_at'    => now()
                ]);
            }
        });
    }
}
