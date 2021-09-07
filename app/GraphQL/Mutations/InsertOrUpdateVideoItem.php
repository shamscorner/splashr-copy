<?php

namespace App\GraphQL\Mutations;

use App\Models\Video\VideoItem;
use App\Utils\VideoItemUtils;
use Carbon\Carbon;

class InsertOrUpdateVideoItem
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $videoItemsArr = [];

        foreach ($args['video_items'] as $videoItem) {
            $videoItemData = VideoItem::query()
                ->where('id', $videoItem['id'])
                ->first();

            if ($videoItemData) {
                if ($videoItemData->status != VideoItemUtils::STATUS_PAID) {
                    $videoItemData->update($videoItem);
                }
            } else {
                // ! remove this line later after implementing the frame.io webhook
                if ($videoItem['status'] == VideoItemUtils::STATUS_APPROVED) {
                    $videoItem['paid_on'] = Carbon::now();
                }

                $videoItemData = VideoItem::create($videoItem);
            }

            array_push($videoItemsArr, $videoItemData);
        }

        return $videoItemsArr;
    }
}
