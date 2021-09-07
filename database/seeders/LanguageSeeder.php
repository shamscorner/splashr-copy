<?php

namespace Database\Seeders;

use App\Models\Video\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Language::create([
            'name' => 'French',
            'is_verified' => true
        ]);
        Language::create([
            'name' => 'English',
            'is_verified' => true
        ]);
        Language::create([
            'name' => 'Spanish',
            'is_verified' => true
        ]);
        Language::create([
            'name' => 'German',
            'is_verified' => true
        ]);
    }
}
