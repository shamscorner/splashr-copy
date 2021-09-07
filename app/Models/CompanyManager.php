<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyManager extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'manager_id',
        'has_seen'
    ];

    protected $table = 'company_manager';
}
