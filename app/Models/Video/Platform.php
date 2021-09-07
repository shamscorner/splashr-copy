<?php

namespace App\Models\Video;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Platform extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'is_verified'];

    protected $hidden = ['pivot'];

    public function videos()
    {
        return $this->belongsToMany(Video::class);
    }
}
