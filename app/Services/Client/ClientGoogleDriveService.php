<?php

namespace App\Services\Client;

use App\Models\Company;
use App\Services\Google\GoogleDriveAPIService;

class ClientGoogleDriveService
{
    private $googleDriveService;
    // public $clientFolderId;

    public function __construct()
    {
        $this->googleDriveService = new GoogleDriveAPIService();
        $this->clientFolderId = config('filesystems.disks.google.clientFolderId');
    }

    public function makeInitialDirectory(Company $company)
    {
        // create folders if not exists
        $companyFolderName = $company->name;
        // find the folder
        $companyFolder = $this->googleDriveService->listFolders([
            'name' => [
                'operator' => '=',
                'value' => $companyFolderName
            ],
            'parents' => [$this->clientFolderId]
        ])->files;

        if (!$companyFolder) {
            // create company folder
            $companyFolderData = $this->googleDriveService->createFolder(
                $companyFolderName,
                [$this->clientFolderId],
            );
            // create media library folder inside the company folder
            $companyMediaLibraryFolderData = $this->googleDriveService->createFolder(
                'MEDIA LIBRARY',
                [$companyFolderData->id]
            );
            // create projects folder inside the company folder
            $companyProjectsFolderData = $this->googleDriveService->createFolder(
                'Projects',
                [$companyFolderData->id]
            );

            // update company data
            $company->update([
                'g_folder_id' => $companyFolderData->id,
                'g_media_folder_id' => $companyMediaLibraryFolderData->id,
                'g_projects_folder_id' => $companyProjectsFolderData->id
            ]);

            // create assets folders in media library folder
            $assetsFolders = [];
            $folderNames = ['Fonts', 'Logos', 'Videos', 'Sounds', 'Graphic Charters'];
            foreach ($folderNames as $folderName) {
                $folder = $this->googleDriveService->createFolder(
                    $folderName,
                    [$companyMediaLibraryFolderData->id]
                );
                array_push($assetsFolders, [
                    'id' => $folder->id,
                    'name' => $folderName
                ]);
            }
            // batch create folders
            $company->folders()->createMany($assetsFolders);
        }
    }
}
