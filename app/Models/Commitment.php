<?php

namespace App\Models;

use App\Jobs\AuthorizeCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Commitment extends Model
{
    use HasFactory;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'company_id',

        'motion_quantity_master',
        'motion_quantity_iteration',
        'motion_quantity_rich_content',

        'acting_quantity_master',
        'acting_quantity_iteration',
        'acting_quantity_rich_content',

        'motion_price_master',
        'motion_price_iteration',
        'motion_price_rich_content',

        'acting_price_master',
        'acting_price_iteration',
        'acting_price_rich_content',

        'used_motion_quantity_master',
        'used_motion_quantity_iteration',
        'used_motion_quantity_rich_content',

        'used_acting_quantity_master',
        'used_acting_quantity_iteration',
        'used_acting_quantity_rich_content',
    ];

    public $incrementing = false;

    protected static function booted()
    {
        static::saved(function ($commitment) {
            AuthorizeCompany::dispatch($commitment);
        });
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where('id', $value)->firstOrFail();
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
