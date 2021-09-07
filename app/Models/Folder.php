<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'name', 'folderable_id', 'folderable_type'];

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

    public function folderable()
    {
        return $this->morphTo();
    }
}
