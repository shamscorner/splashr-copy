<?php

namespace App\Services\Google;

use Exception;
use App\Utils\GoogleDriveFileUtils;

class GoogleDriveAPIService
{
    private $service;

    public function __construct()
    {
        $client = new \Google_Client();
        $client->setApplicationName('splashr drives');
        $client->setAuthConfig(GoogleDriveFileUtils::getOAuthCredentialsFile());
        $client->setRedirectUri(env('APP_URL'));
        $client->addScope("https://www.googleapis.com/auth/drive");
        $client->addScope("https://www.googleapis.com/auth/drive.file");

        $this->service = new \Google_Service_Drive($client);
    }

    /**
     * Create permission for a specific file or folder
     * 
     * @param string $fileId
     * @param string $email
     * 
     * @return mixed
     */
    public function createPermission(string $fileId, string $email)
    {
        $postBody = new \Google_Service_Drive_Permission();
        $postBody->setRole('writer');
        $postBody->setType('user');
        $postBody->setEmailAddress($email);

        return $this->service->permissions->create($fileId, $postBody, [
            'supportsAllDrives' => true,
            'sendNotificationEmail' => false
        ]);
    }


    /**
     * Get a file by a specified file id
     * 
     * @param string $fileId
     * @param array $optParams: []
     * 
     * @return mixed
     */
    public function getFile(string $fileId, array $optParams = [])
    {
        $params = array_merge([
            'fields' => 'id, name, thumbnailLink, mimeType, fileExtension',
            'supportsAllDrives' => true
        ], $optParams);
        try {
            return $this->service->files->get($fileId, $params);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Create a folder in a specific parent folder
     * 
     * @param string $name
     * @param array $parentFolderIds
     * 
     * @return \Google_Service_Drive_DriveFile
     */
    public function createFolder(string $name, array $parentFolderIds): \Google_Service_Drive_DriveFile
    {
        $folder = new \Google_Service_Drive_DriveFile();

        $folder->setName($name);
        $folder->setMimeType('application/vnd.google-apps.folder');
        $folder->setParents($parentFolderIds);

        return $this->service->files->create($folder, [
            'supportsAllDrives' => true
        ]);
    }

    /**
     * Upload a file into a specific parent folder
     * 
     * @param string $name
     * @param array $parentFolderIds
     * @param $fileContent
     * 
     * @return \Google_Service_Drive_DriveFile
     */
    public function uploadFile(string $name, array $parentFolderIds, $fileContent): \Google_Service_Drive_DriveFile
    {
        $file = new \Google_Service_Drive_DriveFile();

        $file->setName($name);
        $file->setParents($parentFolderIds);

        return $this->service->files->create($file, [
            'data' => $fileContent,
            'uploadType' => 'media',
            'fields' => 'id, thumbnailLink',
            'supportsAllDrives' => true
        ]);
    }

    /**
     * Create a shortcut for a specified file
     * 
     * @param string $targetFileId
     * @param string $parentFolderId
     * 
     * @return \Google_Service_Drive_DriveFile
     */
    public function createFileShortcut(string $targetFileId, string $parentFolderId): \Google_Service_Drive_DriveFile
    {
        $file = new \Google_Service_Drive_DriveFile();

        $file->setMimeType('application/vnd.google-apps.shortcut');
        $file->setParents([$parentFolderId]); // shortcuts can be created in one location at a time

        $shortcutDetails = new \Google_Service_Drive_DriveFileShortcutDetails();
        $shortcutDetails->setTargetId($targetFileId);

        $file->setShortcutDetails($shortcutDetails);

        return $this->service->files->create($file, [
            'supportsAllDrives' => true
        ]);
    }

    /**
     * List of all files in a specific folder
     * 
     * @param array $optParams
     * 
     * @return \Google_Service_Drive_FileList
     */
    public function listFilesInAFolder(array $acceptTarget, bool $isShortcutDetails = false, array $optParams = []): \Google_Service_Drive_FileList
    {
        if (!$acceptTarget) {
            $acceptTarget = [
                'operator' => '!=',
                'value' => 'application/vnd.google-apps.folder',
            ];
        }

        $shortcutDetailsQuery = $isShortcutDetails ? ', shortcutDetails' : '';

        $params = [
            'q' => [
                'mimeType' => $acceptTarget,
                'trashed' => [
                    'operator' => '=',
                    'value' => false
                ]
            ],
            'fields' => 'nextPageToken, files(id, name, thumbnailLink, mimeType, fileExtension' . $shortcutDetailsQuery . ')',
            // 'fields' => '*',
        ];

        return $this->getFileList($params, $optParams);
    }

    /**
     * List of all folders
     * 
     * @param array $optParams
     * 
     * @return \Google_Service_Drive_FileList
     */
    public function listFolders(array $optParams = []): \Google_Service_Drive_FileList
    {
        $params = [
            'q' => [
                'mimeType' => [
                    'operator' => '=',
                    'value' => 'application/vnd.google-apps.folder'
                ],
                'trashed' => [
                    'operator' => '=',
                    'value' => false
                ]
            ],
            'fields' => 'nextPageToken, files(id, name)'
        ];

        return $this->getFileList($params, $optParams);
    }

    /**
     * Update a file's name by its id
     * 
     * @param string $fileId
     * @param string $name
     * @param array $optParams
     * 
     * @return \Google_Service_Drive_DriveFile
     */
    public function updateFileName(string $fileId, string $name, array $optParams = []): \Google_Service_Drive_DriveFile
    {
        $params = array_merge([
            'fields' => 'id',
            'supportsAllDrives' => true
        ], $optParams);

        $fileBody = new \Google_Service_Drive_DriveFile();
        $fileBody->setName($name);

        return $this->service->files->update($fileId, $fileBody, $params);
    }

    /**
     * Move a file to trash or restore from trash 
     * 
     * @param string $fileId
     * @param bool $isTrashed
     * 
     * @return \Google_Service_Drive_DriveFile
     */
    public function updateFileTrashedOrNot(string $fileId, bool $isTrashed): \Google_Service_Drive_DriveFile
    {
        $fileBody = new \Google_Service_Drive_DriveFile();

        $fileBody->setTrashed($isTrashed);

        return $this->service->files->update($fileId, $fileBody, [
            'fields' => 'id',
            'supportsAllDrives' => true
        ]);
    }

    /**
     * Delete a file permanently
     * 
     * @param string $fileId
     * 
     * @return bool
     */
    public function deleteFile(string $fileId): bool
    {
        try {
            $this->service->files->delete($fileId, [
                'supportsAllDrives' => true
            ]);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * list all the comments of a specified file
     * 
     * @param string $fileId
     * @param array $optParams
     * 
     * @return \Google_Service_Drive_CommentList|null
     */
    public function listComments(string $fileId, array $optParams = ['pageSize' => 20]): ?\Google_Service_Drive_CommentList
    {
        $params = array_merge([
            'fields' => 'nextPageToken, comments(id, kind, anchor, htmlContent, createdTime, modifiedTime, author, quotedFileContent, replies, resolved)'
        ], $optParams);

        try {
            $comments = $this->service->comments->listComments($fileId, $params);
            return $comments;
        } catch (\Exception $error) {
            return null;
        }
    }

    /**
     * list all comments of a specified page in a file
     * 
     * @param string $fileId
     * @param string $pageId
     * @param int $perPageComments
     * @param string $nextPageToken
     * 
     * @return array|null
     */
    public function listCommentsOfAPage(string $fileId, string $pageId, int $perPageComments = 20, string $nextPageToken = ''): ?array
    {
        $finalComments = [];

        do {
            // get comments
            $commentsWrapper = $this->listComments($fileId, [
                'pageSize' => 20, 'pageToken' => $nextPageToken
            ]);

            if (!$commentsWrapper) {
                return null;
            }

            // set next page token
            $nextPageToken = $commentsWrapper->nextPageToken;
            // filter comments by page id
            $filteredComments = array_filter($commentsWrapper->comments, function ($comment) use ($pageId) {
                if ($comment->anchor) {
                    $anchor = json_decode($comment->anchor);

                    if (isset($anchor->pages)) {
                        if (in_array($pageId, $anchor->pages)) {
                            return true;
                        }
                    } else if (isset($anchor->page)) {
                        if ($anchor->page == $pageId) {
                            return true;
                        }
                    }
                }
            });
            // merge result
            $finalComments = array_merge($finalComments, $filteredComments);

            // not take more than comments per page
            if (sizeof($finalComments) >= $perPageComments) {
                break;
            }
        } while ($nextPageToken);

        return [
            'nextPageToken' => $nextPageToken,
            'comments' => array_slice($finalComments, 0, $perPageComments)
        ];
    }

    /**
     * create a comment in a specified file
     * 
     * @param string $fileId
     * @param array $body
     * @param array $optParams
     * 
     * @return \Google_Service_Drive_Comment|null
     */
    public function createComment(string $fileId, array $body, array $optParams = []): ?\Google_Service_Drive_Comment
    {
        $params = array_merge([
            'fields' => 'id, kind, anchor, htmlContent, createdTime, modifiedTime, author, quotedFileContent, replies, resolved'
        ], $optParams);

        $drive = new \Google_Service_Drive_Comment();
        $drive->setContent($body['content']);
        $drive->setAnchor(json_encode([
            'r' => 'head', // required
            'a' => [], // required
            'type' => 'page', // can be different types, check out the anchoring comments doc
            'uid' => uniqid(), // unique id is necessary for showing on the slide side
            'pages' => [$body['page']], // page id
        ]));

        try {
            $comment = $this->service->comments->create($fileId, $drive, $params);
            return $comment;
        } catch (\Exception $error) {
            return null;
        }
    }

    /**
     * edit a comment in a specified file
     * 
     * @param string $fileId
     * @param string $commentId
     * @param array $body
     * @param array $optParams
     * 
     * @return \Google_Service_Drive_Comment|null
     */
    public function editComment(string $fileId, string $commentId, array $body, array $optParams = []): ?\Google_Service_Drive_Comment
    {
        $params = array_merge([
            'fields' => 'id, kind, anchor, htmlContent, createdTime, modifiedTime, author, quotedFileContent, replies, resolved'
        ], $optParams);

        $drive = new \Google_Service_Drive_Comment();
        $drive->setContent($body['content']);

        try {
            $comment = $this->service->comments->update($fileId, $commentId, $drive, $params);
            return $comment;
        } catch (\Exception $error) {
            return null;
        }
    }

    /**
     * edit a comment in a specified file
     * 
     * @param string $fileId
     * @param string $commentId
     * @param array $body
     * @param array $optParams
     * 
     * @return \Google_Service_Drive_Comment|null
     */
    public function resolveComment(string $fileId, string $commentId, array $body, array $optParams = []): ?\Google_Service_Drive_Comment
    {
        $params = array_merge([
            'fields' => 'id, kind, anchor, htmlContent, createdTime, modifiedTime, author, quotedFileContent, replies, resolved'
        ], $optParams);

        $drive = new \Google_Service_Drive_Comment();
        $drive->setContent($body['content']);
        $drive->setResolved($body['resolved']);

        try {
            $comment = $this->service->comments->update($fileId, $commentId, $drive, $params);
            return $comment;
        } catch (\Exception $error) {
            return null;
        }
    }

    /**
     * delete a comment in a specified file 
     * 
     * @param string $fileId
     * @param string $commentId
     * 
     * @return bool
     */
    public function deleteComment(string $fileId, string $commentId): bool
    {
        try {
            $this->service->comments->delete($fileId, $commentId);
            return true;
        } catch (\Exception $error) {
            return false;
        }
    }

    /**
     * create a reply to a specified comment in a file
     * 
     * @param string $fileId
     * @param string $commentId
     * @param array $body
     * @param array $optParams
     * 
     * @return \Google_Service_Drive_Reply|null
     */
    public function createReplyToComment(string $fileId, string $commentId, array $body, array $optParams = []): ?\Google_Service_Drive_Reply
    {
        $params = array_merge([
            'fields' => 'id, kind, htmlContent, createdTime, modifiedTime, author'
        ], $optParams);

        $drive = new \Google_Service_Drive_Reply();
        $drive->setContent($body['content']);

        try {
            $reply = $this->service->replies->create($fileId, $commentId, $drive, $params);
            return $reply;
        } catch (\Exception $error) {
            return null;
        }
    }

    /**
     * edit a reply to a specified comment in a file
     * 
     * @param string $fileId
     * @param string $commentId
     * @param string $replyId
     * @param array $body
     * @param array $optParams
     * 
     * @return \Google_Service_Drive_Reply|null
     */
    public function editReplyOfComment(string $fileId, string $commentId, string $replyId, array $body, array $optParams = []): ?\Google_Service_Drive_Reply
    {
        $params = array_merge([
            'fields' => 'id, kind, htmlContent, createdTime, modifiedTime, author'
        ], $optParams);

        $drive = new \Google_Service_Drive_Reply();
        $drive->setContent($body['content']);

        try {
            $reply = $this->service->replies->update($fileId, $commentId, $replyId, $drive, $params);
            return $reply;
        } catch (\Exception $error) {
            return null;
        }
    }

    /**
     * delete reply from a specified comment in a file
     * 
     * @param string $fileId
     * @param string $commentId
     * @param string $replyId
     * 
     * @return bool 
     */
    public function deleteReplyOfComment(string $fileId, string $commentId, string $replyId): bool
    {
        try {
            $this->service->replies->delete($fileId, $commentId, $replyId);
            return true;
        } catch (\Exception $error) {
            return false;
        }
    }

    public function listAllRevision(string $fileId, $optParams = [])
    {
        $params = array_merge([
            'fields' => '*'
        ], $optParams);

        try {
            return $this->service->revisions->listRevisions($fileId, $params);
        } catch (\Exception $error) {
            return null;
        }
    }

    public function getLastRevision(string $fileId, $optParams = [])
    {
        $params = array_merge([
            'fields' => '*'
        ], $optParams);

        try {
            return end($this->service->revisions->listRevisions($fileId, $params)->revisions);
        } catch (\Exception $error) {
            return null;
        }
    }

    public function updateRevision(string $fileId, string $revisionId, $optParams = [])
    {
        $params = array_merge([
            'fields' => '*'
        ], $optParams);

        $drive = new \Google_Service_Drive_Revision();
        $drive->setPublished(true);
        $drive->setPublishAuto(true);
        $drive->setPublishedOutsideDomain(true);

        try {
            $revision = $this->service->revisions->update($fileId, $revisionId, $drive, $params);
            return $revision;
        } catch (\Exception $error) {
            return null;
        }
    }

    /**
     * Process and get the file list from google drive
     * 
     * @param array $params
     * @param array $optParams
     * 
     * @return 
     */
    private function getFileList(array $params, array $optParams): \Google_Service_Drive_FileList
    {
        // TODO: support multiple parents later
        $optParams['parents'] = [
            'operator' => '=',
            'value' => $optParams['parents'][0]
        ];

        $params['q'] = array_merge($params['q'], $optParams);

        $paramKeyValues = [];
        foreach ($params['q'] as $key => $value) {
            if (is_bool($value['value'])) {
                $value['value'] = $value['value'] ? 'true' : 'false';
            } else {
                $value['value'] = "'" . $value['value'] . "'";
            }
            array_push($paramKeyValues, $key . ' ' . $value['operator'] . ' ' . $value['value']);
        }

        $params['q'] = implode(' and ', $paramKeyValues);

        // support multiple shared drive
        $params['driveId'] = config('filesystems.disks.google.appDriveId');
        $params['corpora'] = 'drive';
        $params['includeItemsFromAllDrives'] = true;
        $params['supportsAllDrives'] = true;

        return $this->service->files->listFiles($params);
    }
}
