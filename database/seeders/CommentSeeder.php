<?php

namespace Database\Seeders;

use App\Models\Order\Comment;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Comment::factory(200)->create();
    }
}
