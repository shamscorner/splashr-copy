<?php

namespace App\Listeners;

use App\Events\VideoCreated;
use App\Services\Video\VideoGoogleDriveService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateGoogleDriveInitialProjectFolders implements ShouldQueue
{
    /**
     * The time (seconds) before the job should be processed.
     *
     * @var int
     */
    public $delay = 5;

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
     * @param  VideoCreated  $event
     * @return void
     */
    public function handle(VideoCreated $event)
    {
        (new VideoGoogleDriveService())->makeInitialDirectory($event->video);
    }
}
