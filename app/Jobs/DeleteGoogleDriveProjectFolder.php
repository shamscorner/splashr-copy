<?php

namespace App\Jobs;

use App\Models\Video\Video;
use Illuminate\Bus\Queueable;
use App\Services\Video\VideoService;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\Google\GoogleDriveAPIService;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class DeleteGoogleDriveProjectFolder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Video $video;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Video $video)
    {
        $this->video = $video;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $projectFolderId = (new VideoService())->getVideoProjectFolderId($this->video);

        if ($projectFolderId) {
            (new GoogleDriveAPIService())->deleteFile($projectFolderId);
        }
    }
}
