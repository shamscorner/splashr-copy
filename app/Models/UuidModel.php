<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UuidModel extends Model
{
    use HasFactory;

    /**
     * The primary key type is string
     * 
     * @var string
     */
    protected $keyType = 'string';

    /**
     * The incrementing feature is disabled 
     * 
     * @var bool
     */
    public $incrementing = false;

    /**
     * The default initial boot 
     * Create a random unique id as the primary id
     * 
     * @return null
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });
    }

    /**
     * Retrieve the model for a bound value.
     *
     * @param  mixed  $value
     * @param  string|null  $field
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where('id', $value)->firstOrFail();
    }
}
