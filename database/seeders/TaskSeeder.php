<?php

namespace Database\Seeders;

use App\Models\Order\Task;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Task::factory(200)->create();
    }
}
