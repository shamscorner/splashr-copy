<?php

namespace App\Models\Video;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Format extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'ratio', 'viewport', 'icon'];

    protected $hidden = ['pivot'];

    public function videos()
    {
        return $this->belongsToMany(Video::class);
    }
}
