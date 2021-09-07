<?php

namespace App\Services\Video;

use App\Models\Company;
use App\Models\Folder;
use App\Models\Video\Video;
use App\Repositories\Video\VideoRepository;
use App\Services\Google\GoogleDriveAPIService;
use App\Utils\GoogleDriveFileUtils;
use Exception;

class VideoGoogleDriveService
{
    private $googleDriveService;

    public function __construct()
    {
        $this->googleDriveService = new GoogleDriveAPIService();
    }

    public function makeInitialDirectory(Video $video)
    {
        // get the google drive project's folder id
        $company = Company::where('id', $video->company_id)->first();
        $projectFolderId = $company->g_projects_folder_id;

        // create video project folder
        $videoProjectFolderName = $video->name;
        $videoProjectFolder = $this->googleDriveService->createFolder(
            $videoProjectFolderName,
            [$projectFolderId]
        );
        // create creative ideas folder
        $creativeIdeasFolder = $this->googleDriveService->createFolder(
            'Creative Ideas',
            [$videoProjectFolder->id]
        );
        // create assets folder
        $assetsFolder = $this->googleDriveService->createFolder(
            GoogleDriveFileUtils::FOLDER_ASSETS,
            [$videoProjectFolder->id]
        );
        // create assets inner folders
        $assetsInnerFolders = [
            [
                'id' => $videoProjectFolder->id,
                'name' => $videoProjectFolder->name
            ],
            [
                'id' => $creativeIdeasFolder->id,
                'name' => $creativeIdeasFolder->name
            ],
            [
                'id' => $assetsFolder->id,
                'name' => $assetsFolder->name
            ]
        ];
        $folderNames = ['Fonts', 'Logos', 'Videos', 'Sounds', 'Graphic Charters'];
        foreach ($folderNames as $folderName) {
            $folder = $this->googleDriveService->createFolder(
                $folderName,
                [$assetsFolder->id]
            );
            array_push($assetsInnerFolders, [
                'id' => $folder->id,
                'name' => $folderName
            ]);
        }
        // batch create folders
        $video->folders()->createMany($assetsInnerFolders);
    }

    public function updateVideoProjectFolderDataWithGDrive(string $videoId, string $folderName)
    {
        // get the projects folder id
        $videoProjectFolder = (new VideoRepository())->getVideoProjectFolder($videoId, ['id']);

        if ($videoProjectFolder) {
            try {
                // update in the google drive
                $this->googleDriveService->updateFileName($videoProjectFolder->id, $folderName);

                // update the folder data in folders model
                $videoProjectFolder->update([
                    'name' => $folderName
                ]);

                return true;
            } catch (Exception $e) {
                return false;
            }
        }
    }
}
