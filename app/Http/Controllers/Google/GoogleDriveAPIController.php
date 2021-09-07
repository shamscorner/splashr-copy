<?php

namespace App\Http\Controllers\Google;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Google\GoogleDriveAPIService;

class GoogleDriveAPIController extends Controller
{
    private $service;

    public function __construct()
    {
        $this->service = new GoogleDriveAPIService();
    }

    public function createPermission(Request $request)
    {
        $permission = $this->service->createPermission($request->fileId, $request->email);
        return response()->json($permission, 204);
    }

    public function getCommentsOfAFile(Request $request, $fileId)
    {
        // comments per page (int)
        $perPageComments = $request->query('perPage', 20);
        if (!is_numeric($perPageComments)) {
            $perPageComments = 20;
        }

        // next page token (string)
        $nextPageToken = $request->query('nextPageToken');

        $comments = $this->service->listComments($fileId, [
            'pageSize' => $perPageComments, 'pageToken' => $nextPageToken
        ]);

        return response()->json($comments, 200);
    }

    public function getCommentsOfASpecificPage(Request $request, $fileId, $pageId)
    {
        // comments per page (int)
        $perPageComments = $request->query('perPage', 20);
        if (!is_numeric($perPageComments)) {
            $perPageComments = 20;
        }

        // next page token (string)
        $nextPageToken = $request->query('nextPageToken');

        $comments = $this->service->listCommentsOfAPage($fileId, $pageId, $perPageComments, $nextPageToken);

        return response()->json($comments, 200);
    }

    public function createCommentOnAFile(Request $request, $fileId)
    {
        // todo: add validation later

        $comment = $this->service->createComment($fileId, [
            'content' => $request->input('content'),
            'page' => $request->input('page')
        ]);

        if ($comment) {
            return response()->json($comment, 201);
        }

        return response()->json(false, 201);
    }

    public function updateCommentInAFile(Request $request, $fileId, $commentId)
    {
        // todo: add validation later

        $comment = $this->service->editComment($fileId, $commentId, [
            'content' => $request->input('content'),
        ]);

        if ($comment) {
            return response()->json($comment, 200);
        }

        return response()->json(false, 200);
    }

    public function resolveCommentInAFile(Request $request, $fileId, $commentId)
    {
        // todo: add validation later

        $comment = $this->service->resolveComment($fileId, $commentId, [
            'resolved' => $request->input('resolved'),
            'content' => $request->input('content')
        ]);

        if ($comment) {
            return response()->json($comment, 200);
        }

        return response()->json(false, 200);
    }

    public function deleteCommentFromAFile($fileId, $commentId)
    {
        $response = $this->service->deleteComment($fileId, $commentId);

        if ($response) {
            return response()->json(true, 200);
        }

        return response()->json(false, 200);
    }

    public function createReplyOnACommentInAFile(Request $request, $fileId, $commentId)
    {
        // todo: add validation later

        $reply = $this->service->createReplyToComment($fileId, $commentId, [
            'content' => $request->input('content')
        ]);

        if ($reply) {
            return response()->json($reply, 201);
        }

        return response()->json(false, 201);
    }

    public function updateReplyOnACommentInAFile(Request $request, $fileId, $commentId, $replyId)
    {
        // todo: add validation later

        $reply = $this->service->editReplyOfComment($fileId, $commentId, $replyId, [
            'content' => $request->input('content')
        ]);

        if ($reply) {
            return response()->json($reply, 200);
        }

        return response()->json(false, 200);
    }

    public function deleteReplyOfACommentInAFile($fileId, $commentId, $replyId)
    {
        $response = $this->service->deleteReplyOfComment($fileId, $commentId, $replyId);

        if ($response) {
            return response()->json(true, 200);
        }

        return response()->json(false, 200);
    }
}
