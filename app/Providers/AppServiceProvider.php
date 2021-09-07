<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment('local')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // bind google drive api interface -> google drive api repository
        $this->app->bind(
            'App\Repositories\GoogleDriveApi\DriveApiInterface',
            'App\Repositories\GoogleDriveApi\DriveApiRepository'
        );
        // bind video repository interface -> video repository
        $this->app->bind(
            'App\Repositories\Video\VideoRepositoryInterface',
            'App\Repositories\Video\VideoRepository'
        );
        // bind Client repository interface -> Client repository
        $this->app->bind(
            'App\Repositories\Client\ClientRepositoryInterface',
            'App\Repositories\Client\ClientRepository'
        );
        // bind Manager repository interface -> Manager repository
        $this->app->bind(
            'App\Repositories\Manager\ManagerRepositoryInterface',
            'App\Repositories\Manager\ManagerRepository'
        );
    }
}
