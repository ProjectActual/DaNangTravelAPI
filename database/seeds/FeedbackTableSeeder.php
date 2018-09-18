<?php

use Illuminate\Database\Seeder;
use App\Entities\Feedback;

class FeedbackTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Feedback::truncate();
        factory(Feedback::class, 15)->create();
    }
}
