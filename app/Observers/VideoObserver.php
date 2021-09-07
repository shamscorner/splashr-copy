<?php

namespace App\Observers;

use App\Models\Video\Video;
use App\Events\VideoCreated;
use App\Events\VideoSaved;
use App\Jobs\DeleteGoogleDriveProjectFolder;

class VideoObserver
{
    /**
     * Handle the Video "created" event.
     *
     * @param  \App\Models\Video\Video  $video
     * @return void
     */
    public function created(Video $video)
    {
        VideoCreated::dispatch($video);
    }

    /**
     * Handle the Video "saved" event.
     *
     * @param  \App\Models\Video\Video  $video
     * @return void
     */
    public function saved(Video $video)
    {
        VideoSaved::dispatch($video);
    }

    /**
     * Handle the Video "updated" event.
     *
     * @param  \App\Models\Video\Video  $video
     * @return void
     */
    public function updated(Video $video)
    {
        //
    }

    /**
     * Handle the Video "deleted" event.
     *
     * @param  \App\Models\Video\Video  $video
     * @return void
     */
    public function deleted(Video $video)
    {
        DeleteGoogleDriveProjectFolder::dispatch($video);
    }

    /**
     * Handle the Video "restored" event.
     *
     * @param  \App\Models\Video\Video  $video
     * @return void
     */
    public function restored(Video $video)
    {
    }

    /**
     * Handle the Video "force deleted" event.
     *
     * @param  \App\Models\Video\Video  $video
     * @return void
     */
    public function forceDeleted(Video $video)
    {
        //
    }
}
