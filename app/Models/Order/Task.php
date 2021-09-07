<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'order_id',
        'user_id',
        'is_completed'
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
