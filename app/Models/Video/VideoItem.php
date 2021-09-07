<?php

namespace App\Models\Video;

use App\Models\Company;
use App\Models\Order\Order;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VideoItem extends Model
{
    use HasFactory;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'company_id',
        'video_id',
        'order_id',
        'name',
        'type',
        'status',
        'paid_on',
        'url',
        'reference_number'
    ];

    public $incrementing = false;

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where('id', $value)->firstOrFail();
    }

    public function video(): BelongsTo
    {
        return $this->belongsTo(Video::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
