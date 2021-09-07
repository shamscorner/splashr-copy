<?php

namespace App\Models\Order;

use App\Models\Manager;
use App\Models\UuidModel;
use App\Models\Video\Video;
use App\Models\Video\VideoItem;
use App\Utils\VideoItemUtils;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends UuidModel
{
    protected $fillable = [
        'video_id',
        'deadline',
        'is_completed',
        'frameio_project_id'
    ];

    public function video(): BelongsTo
    {
        return $this->belongsTo(Video::class);
    }

    public function managers(): BelongsToMany
    {
        return $this->belongsToMany(Manager::class)
            ->as('manager_order_pivot')
            ->withPivot('id', 'is_visited');
    }

    public function creativeDocuments(): HasMany
    {
        return $this->hasMany(CreativeDocument::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function richContents(): HasMany
    {
        return $this->hasMany(VideoItem::class)
            ->where('type', VideoItemUtils::TYPE_RICH_CONTENT);
    }
}
