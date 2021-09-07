<?php

namespace Database\Seeders;

use App\Models\Video\Format;
use App\Models\Video\Platform;
use App\Models\Video\Purpose;
use App\Models\Video\Video;
use Illuminate\Database\Seeder;

class VideoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $videos = Video::factory(50)->create();

        // sync videos with formats
        $formats = Format::all();
        $videos->each(function ($video, $key) use ($formats) {
            $video->formats()->sync($formats->random());
        });

        // sync videos with purposes 
        $purposes = Purpose::all();
        $videos->each(function ($video, $key) use ($purposes) {
            $video->purposes()->sync($purposes->random(rand(1, count($purposes))));
        });

        // sync videos with platforms 
        $platforms = Platform::all();
        $videos->each(function ($video, $key) use ($platforms) {
            $video->platforms()->sync($platforms->random(rand(1, count($platforms))));
        });
    }
}
