<?php

namespace App\GraphQL\Mutations;

use Illuminate\Support\Str;
use App\Utils\VideoItemUtils;
use App\Models\Video\VideoItem;
use App\Jobs\DecrementUsedCredit;
use App\Jobs\IncrementUsedCredit;

class ToggleRichContent
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $payload = $args['video_item'];
        $payload['session_type'] = $args['video_session_type'];

        if ($args['is_increment']) {
            $args['video_item']['id'] = Str::uuid();

            VideoItem::create($args['video_item']);

            IncrementUsedCredit::dispatch($payload);
        } else {
            $isDeleted = VideoItem::query()
                ->where('company_id', $args['video_item']['company_id'])
                ->where('video_id', $args['video_item']['video_id'])
                ->where('order_id', $args['video_item']['order_id'])
                ->where('type', VideoItemUtils::TYPE_RICH_CONTENT)
                ->where('status', '!=', VideoItemUtils::STATUS_PAID)
                ->latest()
                ->take(1)
                ->delete();

            if ($isDeleted) {
                DecrementUsedCredit::dispatch($payload);
            }
        }
        return 'Rich content updated';
    }
}
