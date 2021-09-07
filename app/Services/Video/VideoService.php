<?php

namespace App\Services\Video;

use App\Models\Client;
use App\Utils\VideoUtils;
use App\Models\Video\Video;
use App\Utils\GoogleDriveFileUtils;
use App\Models\Video\RememberedAnswer;
use App\Repositories\Video\VideoRepository;
use Illuminate\Database\Eloquent\Collection;

class VideoService
{
    public function getVideoStepData(string $step, string $videoId)
    {
        $videoInterface = new VideoRepository();

        $videoData = [];
        $company = null;
        $data = [];

        switch ($step) {
            case VideoUtils::STEP_GENERAL:
                // get video data
                $videoData = $videoInterface->getAVideo(
                    $videoId,
                    ['id', 'client_id', 'company_id', 'campaign_id', 'name', 'service_offer', 'audience', 'session_type'],
                    false,
                    false,
                    false,
                    true,
                    true
                );

                // get additional data
                $data = [
                    'campaigns' => $videoInterface->getCampaignsOfAuthenticatedClient(
                        Client::find($videoData['client_id']),
                        ['id', 'name']
                    )
                ];
                $company = $videoData->company()->first();
                break;

            case VideoUtils::STEP_DETAILS:
                // get video data
                $videoData = $videoInterface->getAVideo(
                    $videoId,
                    ['id', 'client_id', 'company_id', 'name', 'key_message', 'is_voice_over', 'other_requirements', 'session_type', 'languages', 'actor_pref'],
                    true,
                    true,
                    false,
                    true
                );

                // get additional data
                $data = [
                    'platforms' => $videoInterface->getPublishPlatforms(false)->values(),
                    'purposes' => $videoInterface->getAllPurposes(),
                    'languages' => $videoInterface->getAllLanguages(false)
                ];

                // get additional data
                $company = $videoData->company()->first();
                break;

            case VideoUtils::STEP_SUMMARY:
                // get video data
                $videoData = $videoInterface->getAVideo(
                    $videoId,
                    ['id', 'client_id', 'company_id', 'name', 'service_offer', 'audience', 'key_message', 'is_voice_over', 'other_requirements', 'session_type', 'languages', 'actor_pref'],
                    true,
                    true,
                    true,
                    true
                );

                // get video assets folder ids
                $data['assets'] = $this->getFoldersData($videoData);
                $company = $videoData->company()->first();

                break;

            case VideoUtils::STEP_ASSETS:
                $videoData = $videoInterface->getAVideo(
                    $videoId,
                    ['id', 'client_id', 'company_id', 'name', 'session_type']
                );
                $company = $videoData->company()->first();
                $data['company_assets'] = $company->folders()->select('id', 'name')->get();
                // get video assets folder ids
                $data['assets'] = $this->getFoldersData($videoData);

                break;

            default:
                $videoData = $videoInterface->getAVideo(
                    $videoId,
                    ['id', 'client_id', 'company_id', 'name', 'service_offer', 'audience', 'session_type']
                );
        }

        return [
            'videoData' => $videoData,
            'data' => $data,
            'company' => $company
        ];
    }

    public function getLastRememberedAnswers(string $clientId): array
    {
        $data = RememberedAnswer::where('client_id', $clientId)
            ->select('answers')
            ->first();

        if ($data) {
            return json_decode(
                $data->answers,
                true
            );
        }

        return [];
    }

    public function getProjectFoldersOfAVideo(Video $videoData, $isCreativeIdeasFolderIncluded = false)
    {
        $folders = $videoData->folders()->select('id', 'name')->get();

        if ($isCreativeIdeasFolderIncluded) {
            // return $folders->filter(function ($folder) {
            //     return in_array($folder->name, GoogleDriveFileUtils::PROJECT_FOLDERS);
            // });
            return $folders;
        }
        return $folders->filter(function ($folder) {
            // return in_array($folder->name, GoogleDriveFileUtils::PROJECT_FOLDERS) && $folder->name != GoogleDriveFileUtils::FOLDER_CREATIVE_IDEAS;
            return $folder->name != GoogleDriveFileUtils::FOLDER_CREATIVE_IDEAS;
        });
    }

    public function getVideoProjectFolderId(Video $video): ?String
    {
        $folders = $this->getProjectFoldersOfAVideo($video);

        $projectFolder = $folders->filter(function ($folder) use ($video) {
            return $folder->name == $video->name;
        })->first();

        return $projectFolder->id;
    }

    public function getFoldersData(Video $videoData)
    {
        $videoProjectFolders = $videoData->folders()->select('id', 'name')->get();

        $projectFolder = $videoProjectFolders
            ->whereNotIn('name', GoogleDriveFileUtils::PROJECT_FOLDERS)
            ->first();

        $folders = [
            'logosFolderId' => $this->getAssetsFolderIdByName(
                $videoProjectFolders,
                GoogleDriveFileUtils::FOLDER_LOGOS
            ),
            'graphicChaptersFolderId' => $this->getAssetsFolderIdByName(
                $videoProjectFolders,
                GoogleDriveFileUtils::FOLDER_GRAPHIC_CHARTERS
            ),
            'fontsFolderId' => $this->getAssetsFolderIdByName(
                $videoProjectFolders,
                GoogleDriveFileUtils::FOLDER_FONTS
            ),
            'soundsFolderId' => $this->getAssetsFolderIdByName(
                $videoProjectFolders,
                GoogleDriveFileUtils::FOLDER_SOUNDS
            ),
            'videosFolderId' => $this->getAssetsFolderIdByName(
                $videoProjectFolders,
                GoogleDriveFileUtils::FOLDER_VIDEOS
            ),
            'assetsFolderId' => $this->getAssetsFolderIdByName(
                $videoProjectFolders,
                GoogleDriveFileUtils::FOLDER_ASSETS
            ),
            'projectFolderId' => $projectFolder ? $projectFolder->id : ''
        ];
        return $folders;
    }

    private function getAssetsFolderIdByName(Collection $videoProjectFolders, string $name): string
    {
        $folder = $videoProjectFolders->where('name', $name)->first();
        if ($folder) {
            return $folder->id;
        }
        return '';
    }
}
