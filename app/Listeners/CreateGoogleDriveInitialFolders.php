<?php

namespace App\Listeners;

use App\Events\CompanyAuthorized;
use App\Services\Client\ClientGoogleDriveService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateGoogleDriveInitialFolders implements ShouldQueue
{
    /**
     * The time (seconds) before the job should be processed.
     *
     * @var int
     */
    // public $delay = 20;

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
     * @param  CompanyAuthorized  $event
     * @return void
     */
    public function handle(CompanyAuthorized $event)
    {
        (new ClientGoogleDriveService())->makeInitialDirectory($event->company);
    }
}
