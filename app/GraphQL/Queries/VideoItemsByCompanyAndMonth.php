<?php

namespace App\GraphQL\Queries;

use App\Models\Video\VideoItem;
use App\Utils\VideoItemUtils;

class VideoItemsByCompanyAndMonth
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $month = $args['month'];

        return VideoItem::query()
            ->where('company_id', $args['company_id'])
            // ->where('status', $args['status'])
            // ->orWhere('status', VideoItemUtils::STATUS_PAID)
            ->whereIn('status', $args['status'])
            ->when($month, function ($query) use ($month) {
                $query->whereMonth('paid_on', '=', $month);
            });
    }
}
