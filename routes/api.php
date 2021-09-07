<?php

use App\Http\Controllers\AssetLibraryController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Google\GoogleDriveAPIController;
use App\Http\Controllers\Google\GoogleSlideAPIController;
use App\Http\Controllers\VideoController;
use Illuminate\Http\Request;

Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::get('user', function (Request $request) {
        return $request->user();
    });

    // api endpoints, version: 1
    Route::prefix('v1')->group(function () {

        /**
         * google slide endpoints
         */
        Route::prefix('google/slide')->group(function () {
            // google slide presentation endpoints
            Route::get('/presentations/{presentationId}/slides', [
                GoogleSlideAPIController::class, 'getSlidesOfAPresentation'
            ]);
            Route::get('/presentations/{presentationId}/slides/{slideId}/thumbnail', [
                GoogleSlideAPIController::class, 'getThumbnailOfASlide'
            ]);
        });

        /**
         * google drive endpoints
         */
        Route::prefix('google/drive')->group(function () {
            Route::get('/token', [
                DashboardController::class, 'gDriveToken'
            ]);

            Route::get('/is-project-folders-created/{video}', [
                VideoController::class, 'isProjectFoldersCreated'
            ]);

            Route::post('/permissions/create', [
                GoogleDriveAPIController::class, 'createPermission'
            ]);

            // google drive files endpoints
            Route::get('/files/{fileId}', [
                AssetLibraryController::class, 'getFileById'
            ]);
            Route::patch('/files/video-projects/{video}/update-name', [
                AssetLibraryController::class, 'updateVideoProjectNameInGoogleDrive'
            ]);
            Route::delete('/files/{fileId}', [
                AssetLibraryController::class, 'deleteFile'
            ]);
            Route::get('/folders/{folderId}/files', [
                AssetLibraryController::class, 'getFilesFromAFolder'
            ]);
            Route::post('/upload/file', [AssetLibraryController::class, 'uploadToGoogleDrive']);
            Route::patch('/files/{fileId}/move-or-restore-in-trash', [AssetLibraryController::class, 'moveOrRestoreInTrash']);

            // Google files Commenting endpoints
            Route::get('/files/{fileId}/comments', [
                GoogleDriveAPIController::class, 'getCommentsOfAFile'
            ]);
            Route::get('/files/{fileId}/pages/{pageId}/comments', [
                GoogleDriveAPIController::class, 'getCommentsOfASpecificPage'
            ]);
            Route::post('/files/{fileId}/comments', [
                GoogleDriveAPIController::class, 'createCommentOnAFile'
            ]);
            Route::patch('/files/{fileId}/comments/{commentId}', [
                GoogleDriveAPIController::class, 'updateCommentInAFile'
            ]);
            Route::patch('/files/{fileId}/comments/{commentId}/resolve', [
                GoogleDriveAPIController::class, 'resolveCommentInAFile'
            ]);
            Route::delete('/files/{fileId}/comments/{commentId}', [
                GoogleDriveAPIController::class, 'deleteCommentFromAFile'
            ]);
            // Google files Replying endpoints
            Route::post('/files/{fileId}/comments/{commentId}/replies', [
                GoogleDriveAPIController::class, 'createReplyOnACommentInAFile'
            ]);
            Route::patch('/files/{fileId}/comments/{commentId}/replies/{replyId}', [
                GoogleDriveAPIController::class, 'updateReplyOnACommentInAFile'
            ]);
            Route::delete('/files/{fileId}/comments/{commentId}/replies/{replyId}', [
                GoogleDriveAPIController::class, 'deleteReplyOfACommentInAFile'
            ]);
        });
    });
});
