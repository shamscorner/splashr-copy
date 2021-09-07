<?php

namespace Database\Seeders;

use App\Models\Order\Activity;
use Illuminate\Database\Seeder;

class ActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Activity::factory(400)->create();
    }
}
