<?php

namespace App\Models;

use App\Models\Folder;
use App\Models\Video\VideoItem;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Company extends UuidModel
{
    protected $fillable = [
        'name',
        'g_folder_id',
        'g_media_folder_id',
        'g_projects_folder_id',
        'total_orders_count',
        'active_orders_count',
        'is_unlocked'
    ];

    public function managers(): BelongsToMany
    {
        return $this->belongsToMany(Manager::class)
            ->as('company_manager_pivot')
            ->withPivot('id', 'has_seen');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function folders()
    {
        return $this->morphMany(Folder::class, 'folderable');
    }

    public function credits(): BelongsToMany
    {
        return $this->belongsToMany(Credit::class)
            ->withPivot([
                'reference_number',
                'quantity',
                'manager_id'
            ])
            ->withTimestamps();
    }

    public function commitment(): HasOne
    {
        return $this->hasOne(Commitment::class);
    }

    public function videoItems(): HasMany
    {
        return $this->hasMany(VideoItem::class);
    }
}
