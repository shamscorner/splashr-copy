<?php

namespace App\GraphQL\Mutations;

use App\Utils\VideoItemUtils;
use App\Models\Video\VideoItem;
use App\Jobs\DecrementUsedCredit;
use App\Jobs\IncrementUsedCredit;

class ToggleVideoItemStatus
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

        $isSucceed = VideoItem::updateOrCreate(
            ['id' => $id],
            $videoItem
        );

        if (!$isSucceed) {
            return 'Failed';
        }

        $videoItem['session_type'] = $args['video_session_type'];

        if ($videoItem['status'] == VideoItemUtils::STATUS_PAID) {
            IncrementUsedCredit::dispatch($videoItem);
        } else {
            DecrementUsedCredit::dispatch($videoItem);
        }

        return 'Updated';
    }
}
