<?php

namespace Database\Seeders;

use App\Models\Credit;
use App\Utils\CreditUtils;
use Illuminate\Database\Seeder;

class CreditSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Credit::create([
            'name' => 'Master',
            'description' => 'lorem ipsum dolor emet, and some more description',
            'type' => CreditUtils::TYPE_VIDEO_CREATION
        ]);
        Credit::create([
            'name' => 'Iteration',
            'description' => 'lorem ipsum dolor emet, and some more description',
            'type' => CreditUtils::TYPE_VIDEO_ITERATION
        ]);
        Credit::create([
            'name' => 'Rich Content',
            'description' => 'lorem ipsum dolor emet, and some more description',
            'type' => CreditUtils::TYPE_RICH_CONTENT
        ]);
    }
}
