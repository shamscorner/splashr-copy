<?php

namespace App\GraphQL\Mutations;

use App\Models\Video\VideoItem;

class ToggleVideoItemType
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $id = $args['video_item']['id'];

        $videoItem = $args['video_item'];
        unset($videoItem['id']);

        VideoItem::updateOrCreate(
            ['id' => $id],
            $videoItem
        );

        return 'Updated';
    }
}
