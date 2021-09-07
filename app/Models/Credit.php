<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Credit extends UuidModel
{
    protected $fillable = [
        'name',
        'description',
        'type'
    ];
}
