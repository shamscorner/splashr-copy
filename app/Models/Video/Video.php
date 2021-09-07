<?php

namespace App\Models\Video;

use App\Models\Client;
use App\Models\Folder;
use App\Models\Company;
use App\Models\Order\Order;
use App\Models\UuidModel;
use App\Models\Video\Campaign;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Video extends UuidModel
{
    protected $fillable = [
        'client_id',
        'campaign_id',
        'company_id',
        'name',
        'service_offer',
        'audience',
        'key_message',
        'is_voice_over',
        'other_requirements',
        'thumbnail',
        'status',
        'pending_side',
        'is_visited',
        'session_type',
        'languages',
        'actor_pref',
        'manager_notes'
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function formats(): BelongsToMany
    {
        return $this->belongsToMany(Format::class);
    }

    public function platforms(): BelongsToMany
    {
        return $this->belongsToMany(Platform::class);
    }

    public function purposes(): BelongsToMany
    {
        return $this->belongsToMany(Purpose::class);
    }

    public function folders()
    {
        return $this->morphMany(Folder::class, 'folderable');
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function order()
    {
        return $this->hasOne(Order::class);
    }
}
