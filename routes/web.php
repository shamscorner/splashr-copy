<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\CreativeDocumentsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\Webhook\FrameIo\HandleVideoLabel;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/privacy-policy', function () {
    return view('privacy');
});

// Route::get('/google-picker-test', function () {
//     return view('g-picker');
// });

// Route::get('/google-file-copy-test', function () {
//     return view('g-file-copy');
// });

// todo: remove later
Route::get('/test', [TestController::class, 'test'])->name('test');

// auth protected
Route::group(['middleware' => ['auth:sanctum', 'verified']], function () {

    // user's dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // admin
    Route::group(['middleware' => 'is-admin'], function () {
        // admin
        Route::group(['prefix' => 'dashboard/admin'], function () {
            Route::get('/', [DashboardController::class, 'admin'])
                ->name('dashboard.admin');
            Route::get('/orders', [DashboardController::class, 'adminOrders'])
                ->name('admin.orders');
            Route::get('/orders/{order}', [DashboardController::class, 'showOrder'])
                ->name('admin.orders.show');
            Route::get('/clients', [DashboardController::class, 'adminClients'])
                ->name('admin.clients');
            Route::get('/clients/{client}/profile', [DashboardController::class, 'showClientProfile'])
                ->name('admin.clients.profile');
            Route::get('/settings', [DashboardController::class, 'adminSettings'])
                ->name('admin.settings');
            Route::get('/settings/users', [DashboardController::class, 'adminSettingsUsers'])
                ->name('admin.settings.users');
            Route::get('/settings/teams', [DashboardController::class, 'adminSettingsTeams'])
                ->name('admin.settings.teams');
            Route::get('/settings/invoices', [DashboardController::class, 'adminSettingsInvoices'])
                ->name('admin.settings.invoices');
        });

        // authorize a user as a client
        Route::get('authorize/managers/{manager}/users/{user}', [
            ManagerController::class, 'authorizeClient'
        ])->name('authorize-client');
    });

    // client
    Route::group(['prefix' => 'dashboard/client', 'middleware' => 'is-client'], function () {
        // dashboard page
        Route::get('/', [DashboardController::class, 'client'])
            ->name('dashboard.client');
        Route::get('/videos', [VideoController::class, 'index'])
            ->name('client.videos.index');
        Route::get('/videos/{video}', [VideoController::class, 'show'])
            ->name('videos.show');
        Route::get('/campaigns', [CampaignController::class, 'index'])
            ->name('client.campaigns.index');
        Route::get('/campaigns/{campaign}/videos', [CampaignController::class, 'show'])
            ->name('client.campaigns.show');
    });

    // admin or client
    Route::group(['prefix' => 'dashboard/', 'middleware' => 'is-admin-or-client'], function () {
        Route::get('/videos/create', [VideoController::class, 'create'])
            ->name('videos.create');
        Route::get('/videos/{video}/create', [VideoController::class, 'videoSteps'])
            ->name('videos.steps');
        Route::get('/videos/{video}/update', [VideoController::class, 'videoSteps'])
            ->name('videos.update');
    });

    // creative proposal - google doc
    Route::get('/orders/{order}/creative-proposal/google-doc/{id}', [
        CreativeDocumentsController::class, 'googleDocFormOfCreativeProposal'
    ])->name('orders.creative-proposal.google-doc');
    // storyboard - google slide
    Route::get('/orders/{order}/storyboard/google-slide/{id}', [
        CreativeDocumentsController::class, 'googleSlideFormOfStoryboard'
    ])->name('orders.storyboard.google-slide');
    // video - frame.io video
    Route::get('/orders/{order}/frameio/{id}', [
        CreativeDocumentsController::class, 'frameIoFormOfStoryboard'
    ])->name('orders.video.frame-io');
});

Route::get('auth/google', [LoginController::class, 'redirectToGoogle'])
    ->name('auth.google');
Route::get('auth/google/callback', [LoginController::class, 'handleGoogleCallback']);
Route::get('register/company-input/{user}', [LoginController::class, 'takeCompanyInput'])
    ->name('register.company-input');
Route::post('register/company-input/{user}/create', [LoginController::class, 'createUserFromGoogleSignIn'])
    ->name('register.company-input.create');

// new user registration invitation
Route::get('/invitation/{token}', [DashboardController::class, 'inviteNewUser'])
    ->name('invite-new-user');
Route::post('/invitation/{token}/register', [RegisterController::class, 'registerNewInvitedUser'])
    ->name('register-new-invited-user');

// frame.io webhooks
Route::post('/api/v1/frameio-update', HandleVideoLabel::class);
