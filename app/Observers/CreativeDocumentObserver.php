<?php

namespace App\Observers;

use App\Models\Order\CreativeDocument;
use App\Repositories\Video\VideoRepository;
use App\Utils\UserUtils;
use App\Utils\VideoUtils;

class CreativeDocumentObserver
{
    /**
     * Handle the CreativeDocument "created" event.
     *
     * @param  \App\Models\Order\CreativeDocument  $creativeDocument
     * @return void
     */
    public function created(CreativeDocument $creativeDocument)
    {
        //
    }

    /**
     * Handle the CreativeDocument "updated" event.
     *
     * @param  \App\Models\Order\CreativeDocument  $creativeDocument
     * @return void
     */
    public function updated(CreativeDocument $creativeDocument)
    {
        //
    }

    /**
     * Handle the CreativeDocument "saved" event.
     *
     * @param  \App\Models\Order\CreativeDocument  $creativeDocument
     * @return void
     */
    public function saved(CreativeDocument $creativeDocument)
    {
        $video = (new VideoRepository())->getVideoByOrderId($creativeDocument->order_id);

        // update the video status
        if ($creativeDocument->type == VideoUtils::TYPE_VIDEO) {
            $video->update([
                'status' => VideoUtils::STATUS_VIDEO_RECEIVED,
                'pending_side' => UserUtils::USER_CLIENT
            ]);
        } else if ($creativeDocument->type == VideoUtils::TYPE_DOC) {
            $video->update([
                'status' => VideoUtils::STATUS_PROPOSAL_RECEIVED,
                'pending_side' => UserUtils::USER_CLIENT
            ]);
        } else if ($creativeDocument->type == VideoUtils::TYPE_SLIDE) {
            $video->update([
                'status' => VideoUtils::STATUS_STORYBOARD_RECEIVED,
                'pending_side' => UserUtils::USER_CLIENT
            ]);
        }
    }

    /**
     * Handle the CreativeDocument "deleted" event.
     *
     * @param  \App\Models\Order\CreativeDocument  $creativeDocument
     * @return void
     */
    public function deleted(CreativeDocument $creativeDocument)
    {
        //
    }

    /**
     * Handle the CreativeDocument "restored" event.
     *
     * @param  \App\Models\Order\CreativeDocument  $creativeDocument
     * @return void
     */
    public function restored(CreativeDocument $creativeDocument)
    {
        //
    }

    /**
     * Handle the CreativeDocument "force deleted" event.
     *
     * @param  \App\Models\Order\CreativeDocument  $creativeDocument
     * @return void
     */
    public function forceDeleted(CreativeDocument $creativeDocument)
    {
        //
    }
}
