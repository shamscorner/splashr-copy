<?php

namespace App\GraphQL\Mutations;

use App\Models\Order\Comment;
use Illuminate\Support\Facades\Auth;

class MarkCommentAsSeen
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $lastComment = Comment::where('order_id', $args['order_id'])
            ->latest()
            ->first();

        if (!$lastComment) {
            return false;
        }

        $seen_by_users = json_decode($lastComment->seen_by);

        if (in_array($args['user_id'], $seen_by_users)) {
            return true;
        }

        array_push($seen_by_users, $args['user_id']);

        $lastComment->update([
            'seen_by' => json_encode($seen_by_users)
        ]);

        return true;
    }
}
