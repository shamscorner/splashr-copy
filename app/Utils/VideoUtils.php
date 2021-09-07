<?php

namespace App\Utils;

use Illuminate\Support\Str;

class VideoUtils
{
    public const STEP_GENERAL = 'General';
    public const STEP_ASSETS = 'Assets';
    public const STEP_DETAILS = 'Details';
    public const STEP_SUMMARY = 'Summary';

    public const STATUS_ORDERED = 1;
    public const STATUS_PENDING = 2;
    public const STATUS_PROPOSAL_RECEIVED = 3;
    public const STATUS_VIDEO_RECEIVED = 4;
    public const STATUS_STORYBOARD_RECEIVED = 5;
    public const STATUS_FINISHED = 6;

    public const TYPE_SLIDE = 1;
    public const TYPE_DOC = 2;
    public const TYPE_VIDEO = 3;

    public const SESSION_MOTION = 'motion';
    public const SESSION_ACTING = 'acting';

    public const CREATIVE_DOC_STATUSES = [
        self::TYPE_SLIDE,
        self::TYPE_DOC,
        self::TYPE_VIDEO
    ];

    public const STEPS = [
        self::STEP_GENERAL,
        self::STEP_ASSETS,
        self::STEP_DETAILS,
        self::STEP_SUMMARY
    ];

    public const STATUS = [
        self::STATUS_ORDERED,
        self::STATUS_PENDING,
        self::STATUS_PROPOSAL_RECEIVED,
        self::STATUS_VIDEO_RECEIVED,
        self::STATUS_STORYBOARD_RECEIVED,
        self::STATUS_FINISHED
    ];

    public static function toValidStep(string $step): string
    {
        return ucwords(str_replace('-', ' ', Str::of($step)->kebab()->__toString()));
    }
}
