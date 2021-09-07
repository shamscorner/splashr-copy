<?php

namespace App\Http\Controllers;

use Exception;
// use Inertia\Inertia;
use App\Models\Video\Video;
use Illuminate\Http\Request;
use App\Utils\GoogleDriveFileUtils;
use App\Services\Google\GoogleDriveAPIService;
use App\Services\Video\VideoGoogleDriveService;
// use App\Repositories\Client\ClientRepositoryInterface;

class AssetLibraryController extends Controller
{
    private $googleDriveService;

    public function __construct()
    {
        $this->googleDriveService = new GoogleDriveAPIService();
    }

    // public function index(ClientRepositoryInterface $clientInterface)
    // {
    //     $company = $clientInterface->getCompanyOfAuthenticatedUser();

    //     $fileList = $this->googleDriveService->listFilesInAFolder(
    //         [],
    //         false,
    //         [
    //             'parents' => [$company->g_media_folder_id]
    //         ]
    //     );

    //     return Inertia::render('AssetLibrary', [
    //         'currentSideNavRoute' => 'assets-library.index',
    //         'currentSideNavName' => 'Assets Library',
    //         'assetsLibrary' => [
    //             'folders' => $company->folders()->select('id', 'name')->get(),
    //             'files' => $this->processFileListForFrontend($fileList->files)
    //         ],
    //         'company' => $company
    //     ]);
    // }

    public function getFileById($fileId)
    {
        try {
            $file = $this->googleDriveService->getFile($fileId);
            return response()->json([
                'file' => $this->processFileListForFrontend([$file])[0]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Something went wrong'
            ], 500);
        }
    }

    public function getFilesFromAFolder(Request $request, $folderId)
    {
        $isShortcutDetails = $request->query('shortcut-details') == 'true' ? true : false;
        $acceptTarget = $request->query('accept-target') ? $request->query('accept-target') : '';

        $fileList = $this->googleDriveService->listFilesInAFolder(
            GoogleDriveFileUtils::getFilteredQuery($acceptTarget),
            $isShortcutDetails,
            [
                'parents' => [$folderId]
            ]
        );

        return response()->json([
            'files' => $this->processFileListForFrontend($fileList->files, $isShortcutDetails)
        ]);
    }

    public function uploadToGoogleDrive(Request $request)
    {
        $request->validate([
            'parent_folder_id' => 'required|string',
            'name' => 'required|string',
            'file' => 'required|file|max:5000000',
        ]);

        $contents = file_get_contents($request->file->path());

        // upload to google drive
        try {
            $uploadedFile = $this->googleDriveService->uploadFile(
                $request->name,
                [$request->parent_folder_id],
                $contents
            );

            return response()->json([
                'id' => $uploadedFile->id,
                'thumbnailLink' => $uploadedFile->thumbnailLink
            ]);
        } catch (Exception $e) {
            return response()->json('Operation has not been implemented', 501);
        }
    }

    public function updateVideoProjectNameInGoogleDrive(Request $request, Video $video)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $isUpdated = (new VideoGoogleDriveService())->updateVideoProjectFolderDataWithGDrive(
            $video->id,
            $request->name
        );

        if ($isUpdated) {
            return response()->json('', 204);
        }
        return response()->json('Operation has not been implemented', 501);
    }

    public function moveOrRestoreInTrash(Request $request, $fileId)
    {
        $request->validate([
            'is_trashed' => 'required|bool',
        ]);

        try {
            $this->googleDriveService->updateFileTrashedOrNot($fileId, $request->is_trashed);
            return response()->json('', 204);
        } catch (Exception $e) {
            return response()->json('Operation has not been implemented', 501);
        }
    }

    public function deleteFile($fileId)
    {
        if ($this->googleDriveService->deleteFile($fileId)) {
            return response()->json('', 204);
        }
        return response()->json('Operation has not been implemented', 501);
    }

    private function processFileListForFrontend($files, $isShortcutDetails = false)
    {
        $filesData = [];
        foreach ($files as $file) {
            $d = [
                'id' => $file->id,
                'alt' => $file->name,
                'ext' => $file->fileExtension,
                'name' =>  $file->name,
                'src' => $file->thumbnailLink,
                'type' =>  $file->mimeType,
            ];
            if ($isShortcutDetails) {
                $d['shortcutDetails'] = [
                    'targetId' => $file->shortcutDetails->targetId,
                    'targetMimeType' => $file->shortcutDetails->targetMimeType
                ];
            }
            array_push($filesData, $d);
        }
        return $filesData;
    }
}
