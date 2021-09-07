<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreativeDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'url',
        'document_id',
        'order_id',
        'is_changed'
    ];
}
