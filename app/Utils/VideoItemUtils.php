<?php

namespace App\Utils;

class VideoItemUtils
{
    public const STATUS_NONE = 0;
    public const STATUS_IN_PROGRESS = 1;
    public const STATUS_NEEDS_REVIEW = 2;
    public const STATUS_APPROVED = 3;
    public const STATUS_PAID = 4;

    public const TYPE_MASTER = 0;
    public const TYPE_ITERATION = 1;
    public const TYPE_RICH_CONTENT = 2;

    public const VIDEO_ITEM_STATUS = [
        'none' => 0,
        'in_progress' => 1,
        'needs_review' => 2,
        'approved' => 3,
    ];
}
