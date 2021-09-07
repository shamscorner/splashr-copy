<?php

namespace App\GraphQL\Mutations;

use App\Models\Video\Video;
use App\Services\Google\GoogleDriveAPIService;
use App\Utils\GoogleDriveFileUtils;
use Exception;
use Illuminate\Support\Str;

class SyncAssetsToProject
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $video = Video::where('id', $args['assets']['video']['id'])->first();

        // get the folders data
        $folders = $video->folders()->get();

        // google drive api service
        $googleDriveService = new GoogleDriveAPIService();

        // list of files in each folders
        $existingFiles = [
            'assets' => [
                'files' => [],
                'isSearched' => false
            ],
            'Fonts' => [
                'files' => [],
                'isSearched' => false
            ],
            'Graphic Charters' => [
                'files' => [],
                'isSearched' => false
            ],
            'Logos' => [
                'files' => [],
                'isSearched' => false
            ],
            'Sounds' => [
                'files' => [],
                'isSearched' => false
            ],
            'videos' => [
                'files' => [],
                'isSearched' => false
            ]
        ];

        // create shortcuts
        foreach ($args['assets']['assets'] as $assetsTarget => $assets) {
            $shortCuts = [];
            $parentFolderName = '';
            foreach ($assets as $asset) {
                if ($asset['isParentFolder']) {
                    // parent assets folder id
                    $parentFolderName = GoogleDriveFileUtils::FOLDER_ASSETS;
                } else {
                    // child folders
                    $parentFolderName = ucwords(Str::of($assetsTarget)->kebab()
                        ->replace('-', ' ')
                        ->__toString());

                    if ($parentFolderName == 'Graphics') {
                        $parentFolderName = 'Graphic Charters';
                    }
                }

                try {
                    // get the parent folder id
                    $parentFolderId = $folders->where('name', $parentFolderName)->first()->id;

                    // get the file list from google drive if required
                    if (!$existingFiles[$parentFolderName]['isSearched']) {
                        // get the file list for this folder
                        $existingFiles[$parentFolderName]['files'] = $googleDriveService->listFilesInAFolder([
                            'operator' => '=',
                            'value' => 'application/vnd.google-apps.shortcut',
                        ], true, [
                            'parents' => [
                                $parentFolderId
                            ]
                        ])->files;

                        // mark as searched
                        $existingFiles[$parentFolderName]['isSearched'] = true;
                    }

                    // check whether the shortcut is available in the current directory or not
                    $isAvailable = false;
                    foreach ($existingFiles[$parentFolderName]['files'] as $existingFile) {
                        if ($existingFile->shortcutDetails->targetId == $asset['id']) {
                            $isAvailable = true;
                            break;
                        }
                    }

                    // create shortcut if not available
                    if (!$isAvailable) {
                        $shortCut = $googleDriveService->createFileShortcut($asset['id'], $parentFolderId);
                        array_push($shortCuts, [
                            'id' => $asset['id'],
                            'shortcut_id' => $shortCut->id
                        ]);
                    }
                } catch (Exception $e) {
                    continue;
                }
            }
        }

        return json_encode($shortCuts);
    }
}
