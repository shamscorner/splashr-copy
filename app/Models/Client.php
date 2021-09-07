<?php

namespace App\Models;

use App\Events\ClientCreated;
use App\Models\Video\Campaign;
use App\Models\Video\RememberedAnswer;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Client extends UuidModel
{
    protected $fillable = ['user_id'];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        // fire the client created event
        static::created(function ($client) {
            ClientCreated::dispatch($client);
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function campaigns(): BelongsToMany
    {
        return $this->belongsToMany(Campaign::class)->withTimestamps();
    }

    public function rememberedAnswer(): HasOne
    {
        return $this->hasOne(RememberedAnswer::class);
    }
}
