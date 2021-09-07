<?php

namespace App\Providers;

use App\Events\VideoSaved;
use App\Models\Order\Order;
use App\Models\Video\Video;
use App\Events\VideoCreated;
use App\Events\ClientCreated;
use App\Events\CompanyAuthorized;
use App\Events\CompanyCreditCreated;
use App\Listeners\AuthorizeCompanyOfClients;
use App\Observers\OrderObserver;
use App\Observers\VideoObserver;
use Illuminate\Auth\Events\Registered;
use App\Listeners\CreateGoogleDriveInitialFolders;
use App\Listeners\UpdateVideoProjectFolderNameInGDrive;
use App\Listeners\CreateGoogleDriveInitialProjectFolders;
use App\Listeners\IncrementAvailableCreditNumber;
use App\Models\Order\CreativeDocument;
use App\Observers\CreativeDocumentObserver;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class
        ],
        // Verified::class => [
        //     SendUserRegisteredNotification::class
        // ],
        CompanyAuthorized::class => [
            CreateGoogleDriveInitialFolders::class
        ],
        VideoCreated::class => [
            CreateGoogleDriveInitialProjectFolders::class
        ],
        VideoSaved::class => [
            UpdateVideoProjectFolderNameInGDrive::class
        ],
        CompanyCreditCreated::class => [
            AuthorizeCompanyOfClients::class,
            IncrementAvailableCreditNumber::class
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Video::observe(VideoObserver::class);
        Order::observe(OrderObserver::class);
        CreativeDocument::observe(CreativeDocumentObserver::class);
    }
}
