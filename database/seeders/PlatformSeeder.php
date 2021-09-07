<?php

namespace Database\Seeders;

use App\Models\Video\Platform;
use Illuminate\Database\Seeder;

class PlatformSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Platform::create([
            'name' => 'Facebook',
            'is_verified' => true
        ]);
        Platform::create([
            'name' => 'Instagram',
            'is_verified' => true
        ]);
        Platform::create([
            'name' => 'Twitter',
            'is_verified' => true
        ]);
        Platform::create([
            'name' => 'Youtube',
            'is_verified' => true
        ]);
        Platform::create([
            'name' => 'TikTok',
            'is_verified' => true
        ]);
        Platform::create([
            'name' => 'Something',
            'is_verified' => false
        ]);
        Platform::create([
            'name' => 'Else',
            'is_verified' => false
        ]);
    }
}
