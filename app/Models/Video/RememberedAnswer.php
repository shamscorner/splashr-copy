<?php

namespace App\Models\Video;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RememberedAnswer extends Model
{
    use HasFactory;

    protected $fillable = ['client_id', 'answers'];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
