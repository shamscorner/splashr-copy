<?php

namespace App\GraphQL\Queries;

use App\Models\Order\Comment;
use Illuminate\Database\Eloquent\Builder;

class CommentsByOrder
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args): Builder
    {
        return Comment::where('order_id', $args['order_id'])
            ->where('comment_id', null)
            ->with('comments')
            ->orderByDesc('updated_at');
    }
}
