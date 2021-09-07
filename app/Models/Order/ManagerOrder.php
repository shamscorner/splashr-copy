<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManagerOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'manager_id',
        'order_id',
        'is_visited'
    ];

    protected $table = 'manager_order';
}
