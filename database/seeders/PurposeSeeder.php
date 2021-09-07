<?php

namespace Database\Seeders;

use App\Models\Video\Purpose;
use Illuminate\Database\Seeder;

class PurposeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Purpose::truncate();

        Purpose::create([
            'name' => 'Branding'
        ]);
        Purpose::create([
            'name' => 'Performance'
        ]);
        Purpose::create([
            'name' => 'Consideration'
        ]);
    }
}
