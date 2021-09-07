<?php

namespace App\Listeners;

use App\Events\VideoSaved;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\Video\VideoGoogleDriveService;

class UpdateVideoProjectFolderNameInGDrive implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  VideoSaved  $event
     * @return void
     */
    public function handle(VideoSaved $event)
    {
        (new VideoGoogleDriveService())->updateVideoProjectFolderDataWithGDrive(
            $event->video->id,
            $event->video->name
        );
    }
}
