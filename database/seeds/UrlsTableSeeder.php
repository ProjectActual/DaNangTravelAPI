<?php

use Illuminate\Database\Seeder;

use App\Entities\Url;
use App\Entities\Post;
use App\Entities\Category;

class UrlsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Url::truncate();

        $this->data();
    }

    public function data()
    {
        Category::all()->each(function ($category) {
            Url::create([
                'url_title'     => $category->name_category,
                'uri'           => $category->uri_category,
                'created_at'           => now(),
                'updated_at'           => now()
            ]);
        });

        Post::all()->each(function ($post) {
            Url::create([
                'url_title'     => $post->title,
                'uri'           => $post->uri_post,
                'created_at'           => now(),
                'updated_at'           => now()
            ]);
        });
    }
}
