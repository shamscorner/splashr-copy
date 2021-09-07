<?php

namespace Database\Seeders;

use App\Models\Video\Format;
use Illuminate\Database\Seeder;

class FormatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Format::create([
            'name' => 'feed',
            'ratio' => '4:5',
            'viewport' => 'mobile',
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 35 62" class="w-full h-3/4"><rect id="Rectangle" width="35" height="62" fill="#e1e1e1" /><rect id="Rectangle-2" data-name="Rectangle" width="35" height="43" transform="translate(0 10)" fill="#a095fd"/></svg>'
        ]);
        Format::create([
            'name' => 'stories',
            'ratio' => '9:16',
            'viewport' => 'mobile',
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 35 62" class="w-full h-3/4"><rect id="Rectangle_Copy_5" data-name="Rectangle Copy 5" width="35" height="62" fill="#a095fd"/></svg>'
        ]);
        Format::create([
            'name' => 'in stream',
            'ratio' => '16:9',
            'viewport' => 'mobile & desktop',
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 127 62" class="w-full h-3/4"><g id="Group_4" data-name="Group 4"><rect id="Rectangle_Copy" data-name="Rectangle Copy" width="36" height="62" fill="#e1e1e1"/><rect id="Rectangle_Copy_3" data-name="Rectangle Copy 3" width="36" height="20" transform="translate(0 21)" fill="#a095fd"/></g><g id="Group" transform="translate(44)"><rect id="Rectangle" width="83" height="62" fill="#e1e1e1" /><rect id="Rectangle_Copy_4" data-name="Rectangle Copy 4" width="83" height="46" transform="translate(0 8)" fill="#a095fd"/></g></svg>'
        ]);
    }
}
